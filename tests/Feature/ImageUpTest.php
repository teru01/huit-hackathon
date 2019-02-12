<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use App\User;

class ImageUpTest extends TestCase
{
    public function test_画像バリデーション() {
        $data = [
            'myImage' => UploadedFile::fake()->image('photo.jpg'),
            'name' => 'aaa'
        ];

        $user = factory(User::class)->create();
        $response = $this->actingAs($user)->postJson(route('image.confirm'), $data);

        $response->assertStatus(200);
    }
}
