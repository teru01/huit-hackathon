<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Photo;

class PhotoDetailApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_正しいJSONを返す() {
        factory(Photo::class)->create();
        $photo = Photo::with(['owner'])->first();

        $expected_data = [
            'id' => $photo->id,
            'url' => $photo->url,
            'owner' => [
                'name' => $photo->owner->name
            ],
        ];

        $response = $this->getJson(route('photo.show', ['id' => $photo->id]));
        $response->assertStatus(200)
            ->assertJsonFragment($expected_data);
    }
}
