<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StorePhoto;
use App\Photo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Comment;
use App\Http\Requests\StoreComment;

class PhotoController extends Controller
{
    public function __construct() {
        $this->middleware('auth')->except(['index', 'download', 'show']);
    }

    public function index() {
        return Photo::with(['owner', 'likes'])->orderBy(Photo::CREATED_AT, 'desc')->paginate();
    }

    public function create(StorePhoto $request) {
        $extension = $request->photo->extension();
        $photo = new Photo();
        $photo->filename = $photo->id . '.' . $extension;
        Storage::cloud()->putFileAs('', $request->photo, $photo->filename, 'public');
        try {
            Auth::user()->photos()->save($photo);
            DB::commit();
        } catch (\Exception $exception) {
            Storage::cloud()->delete($photo->filename);
            DB::rollBack();
            throw $exception;
        }
        return response($photo, 201);
    }

    public function download(Photo $photo) {
        if (!Storage::cloud()->exists($photo->filename)) {
            abort(404);
        }

        $headers = [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment: filename="' . $photo->filename . '"',
        ];

        return response(Storage::cloud()->get($photo->filename), 200, $headers);
    }

    public function show(string $id) {
        $photo = Photo::where('id', $id)->with(['owner', 'comments.author', 'likes'])->first();
        return $photo ?? abort(404);
    }

    public function addComment(Photo $photo, StoreComment $request) {
        $comment = new Comment();
        $comment->content = $request->get('content');
        $comment->user_id = Auth::user()->id;
        $photo->comments()->save($comment);

        $new_comment = Comment::where('id', $comment->id)->with(['author'])->first();

        return response($new_comment, 201);
    }

    private function findPhotoWithLikes(string $id) {
        $photo = Photo::where('id', $id)->with('likes')->first();

        if (!$photo) {
            abort(404);
        }

        return $photo;
    }

    public function like(string $id) {
        $photo = $this->findPhotoWithLikes($id);
        $photo->likes()->detach(Auth::user()->id);
        $photo->likes()->attach(Auth::user()->id);

        return ['photo_id' => $id];
    }

    public function unlike(string $id) {
        $photo = $this->findPhotoWithLikes($id);
        $photo->likes()->detach(Auth::user()->id);
        return ['photo_id' => $id];
    }

    private function makeCSV(array $photos) {
        $fname = 'photos' . date('Ymd') . '.csv';
        $file = fopen(\storage_path("app/public/${fname}"), 'w');
        if($file) {
            foreach ($photos as $line) {
                fputcsv($file, array_values($line));
            }
        }
        fclose($file);
        return $fname;
    }

    public function csvDownload() {
        $photos = Photo::with(['likes'])->orderBy(Photo::CREATED_AT, 'desc')->get()->toArray();
        // $photos = [
        //     ['id' => 1, 'name' => 'teru'],
        //     ['id' => 3, 'name' => 'teru'],
        // ];
        $fname = $this->makeCSV($photos);
        return \response()->download(\storage_path("app/public/${fname}"))->deleteFileAfterSend(true);
    }
}
