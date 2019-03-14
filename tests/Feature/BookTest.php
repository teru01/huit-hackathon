<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Book;

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
        factory(Book::class, 5)->create(['user_id' => $this->user->id]);
        factory(Book::class, 5)->create();
        $response = $this->json('GET', '/api/books/user/'.$this->user->id);
        $response->assertStatus(200)->assertJsonCount(5);
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
}
