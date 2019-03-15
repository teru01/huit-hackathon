<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Book;
use App\BRequest;

class BookTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testFindById()
    {
        $book = factory(Book::class)->create(['user_id' => $this->user->id]);
        factory(BRequest::class, 3)->create(['book_id' => $book->id]);

        $book = Book::with('requests')->first();
        $response = $this->json('GET', '/api/books/user/'.$this->user->id);
        $response->assertStatus(200)->assertJsonFragment([
            'title' => $book->title,
            'requests' => $book->requests->sortByDesc('id')->map(function($req) {
                return [
                    'accepted' => $req->accepted,
                    'book_id' => $req->book_id,
                    'id' => $req->id,
                    'user_id' => $req->user_id,
                ];
            })->all(),
        ]);
    }

    public function testRegister() {
        $book = factory(Book::class)->make();
        $response = $this->actingAs($this->user)->json('POST', '/api/books', $book->toArray());
        $response->assertStatus(201)->assertJsonFragment([
            'user_id' => $this->user->id,
            'title' => $book->title
        ]);

        $addedbook = Book::first();
        $this->assertEquals($addedbook->title, $book->title);
    }

    public function testShow() {
        $book = factory(Book::class)->create();
        $response = $this->json('GET', '/api/books/'.$book->id);
        $response->assertStatus(200)->assertJsonFragment([
            'title' => $book->title,
            'owner' => ['name' => User::find($book->user_id)->name]
        ]);
    }

    public function test_addRequest() {
        $book = factory(Book::class)->create();
        $response = $this->actingAs($this->user)->json('PUT', '/api/requests/'.$book->id);
        $response->assertStatus(201);

        $book = Book::with('requests')->first();
        $response->assertJsonFragment([
            'user_id' => $book->requests->first()->user_id
        ]);
    }

    public function test_accept() {
        $book = factory(Book::class)->create(['user_id' => $this->user->id]);
        $request = factory(BRequest::class)->create(['book_id' => $book->id]);

        $response = $this->actingAs($this->user)->json('PATCH', '/api/requests/'.$request->id);
        $response->assertStatus(200);

        $req = BRequest::find($request->id);
        $this->assertEquals($req->accepted, true);
    }
}
