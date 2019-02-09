<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddCommentTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() {
        parent::setUp();
        $this->user = factory(App\User::class)->create();
    }

    /**
     * 写真を作成し、それに紐づくコメントを投稿
     */
    public function test_コメントを追加できる() {
        factory(Photo::class)->create();
        $photo = Photo::first();

        $content = 'sample content';

        $response = $this->actingAs($this->user)
            ->postJson(route('photo.comment', ['photo' => $photo->id]), compact('content')); //compactは['content' => $content]

        $response->assertStatus(201)
            ->assertJsonFragment([
                "author" => [
                    "name" => $this->user->name,
                ],
                "content" => $content
            ]);

        $comments = $photo->comments()->get();
        $this->assertEquals(1, $comments->count());
        $this->assrtEquals($content, $comments[0]->content);
    }
}
