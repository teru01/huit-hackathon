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
        // factory(Photo::class)->create()->each(function ($photo) {
        //     $photo->comments()->saveMany(factory(Comment::class, 3)->make());
        // });
        // $photo = Photo::first();

        // $expected_data = [
        //     'id' => $photo->id,
        //     'url' => $photo->url,
        //     'owner' => [
        //         'name' => $photo->owner->name
        //     ],
        //     'comments' => $photo->comments->sortByDesc('id')->map(function ($comment) {
        //         return [
        //             'author' => [
        //                 'name' => $comment->author->name,
        //             ],
        //             'content' => $comment->content
        //         ];
        //     })->all(),
        //     'liked_by_user' => false,
        //     'likes_count' => 0
        // ];

        // $response = $this->getJson(route('photo.show', ['id' => $photo->id]));
        // $response->assertStatus(200)
        //     ->assertJsonFragment($expected_data);
        factory(Photo::class)->create()->each(function ($photo) {
            $photo->comments()->saveMany(factory(Comment::class, 3)->make());
        });
        $photo = Photo::first();
        $response = $this->json('GET', route('photo.show', [
            'id' => $photo->id,
        ]));
        
        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $photo->id,
                'url' => $photo->url,
                'owner' => [
                    'name' => $photo->owner->name,
                ],
                'liked_by_user' => false,
                'likes_count' => 0,
                'comments' => $photo->comments
                    ->sortByDesc('id')
                    ->map(function ($comment) {
                        return [
                            'author' => [
                                'name' => $comment->author->name,
                            ],
                            'content' => $comment->content,
                        ];
                    })
                    ->all(),
            ]);
    }
}
