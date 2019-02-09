<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Photo;
use Carbon\Carbon;

class PhotoListApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_正しいJSONを返却() {
        factory(Photo::class, 5)->create();

        $response = $this->getJson(route('photo.index'));
        $photos = Photo::with(['owner'])->orderBy('created_at', 'desc')->get();

        $expected_data = $photos->map(function ($photo) {
            return [
                'id' => $photo->id,
                'url' => $photo->url,
                'owner' => [
                    'name' => $photo->owner->name,
                ]
            ];
        })->all();

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonFragment([
                'data' => $expected_data
            ]);
    }
}
