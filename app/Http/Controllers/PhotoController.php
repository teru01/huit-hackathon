<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StorePhoto;
use App\Photo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class PhotoController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function create(StorePhoto $request) {
        $extension = $request->photo->extension();
        $photo = new Photo();
        $photo->filename = $photo->id . '.' . $extension;
        Storage::cloud()->putFileAs('', $request->photo, $photo->filename, 'public');
        // Storage::cloud()->delete($photo->filename);
        DB::beginTransaction();
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
}
