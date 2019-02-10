<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Photo;
use App\Comment;

class PhotoDetailApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_正しいJSONを返す() {
        factory(Photo::class)->create()->each(function ($photo) {
            $photo->comments()->saveMany(factory(Comment::class, 3)->make());
        });
        $photo = Photo::first();

        $expected_data = [
            'id' => $photo->id,
            'url' => $photo->url,
            'owner' => [
                'name' => $photo->owner->name
            ],
            'comments' => $photo->comments->sortByDesc('id')->map(function ($comment) {
                return [
                    'author' => [
                        'name' => $comment->name
                    ],
                    'content' => $comment->content
                ];
            })
        ];

        $response = $this->getJson(route('photo.show', ['id' => $photo->id]));
        $response->assertStatus(200)
            ->assertJsonFragment($expected_data);
    }
}
