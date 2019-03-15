<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Book;
use Auth;
use App\BRequest;

class BookController extends Controller
{
    public function __construct() {
        $this->middleware('auth')->except(['index', 'usersBook', 'show']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Book::all();
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $book = new Book();
        $book->user_id = Auth::user()->id;
        $book->title = $request->title;
        $book->image_url = $request->image_url;
        $book->author = $request->author;
        $book->published = $request->published;

        $book->save();
        return response($book, 201);
    }

    public function usersBook(int $user_id, Request $request) {
        return Book::where('user_id', $user_id)->with(['requests' => function($q) {
            $q->orderBy('id', 'desc');
        }])->get();
    }

    public function addRequest(Request $request, int $book_id) {
        $oldReq = BRequest::where(['user_id' => Auth::user()->id, 'book_id' => $book_id])->first();
        if($oldReq) {
            BRequest::find($oldReq->id)->delete();
        }

        $borrowReq = new BRequest();
        $borrowReq->user_id = Auth::user()->id;
        $borrowReq->book_id = $book_id;
        $borrowReq->accepted = false;
        $borrowReq->save();

        return response($borrowReq, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Book::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
