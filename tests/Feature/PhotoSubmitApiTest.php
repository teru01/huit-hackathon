<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Photo;
use App\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class PhotoSubmitApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    /**
     * ファイルアップロードで201 created
     * 作成された画像IDが12桁英数字
     * 画像が存在
     */
    public function test_ファイルをアップロードできる() {
        Storage::fake('s3');

        $response = $this->actingAs($this->user)
        ->json('POST', route('photo.create'), [
            // ダミーファイルを作成して送信している
            'photo' => UploadedFile::fake()->image('photo.jpg'),
        ]);
        $response->assertStatus(201);
        $photo = Photo::first();
        $this->assertRegExp('/^[0-9a-zA-Z-_]{12}$/', $photo->id);
        Storage::cloud()->assertExists($photo->filename);
    }

    public function test_ファイル保存エラーの時はDBに挿入しない() {
        Storage::shouldReceive('cloud')
            ->once()
            ->andReturnNull();
        $response = $this->actingAs($this->user)
            ->json('POST', route('photo.create'), ['photo' => UploadedFile::fake()->image('photo.jpg')]);
        $response->assertStatus(500);
        $this->assertEmpty(Photo::all());
    }

    public function test_DBエラーの場合はファイルを保存しない() {
        Schema::drop('likes');
        Schema::drop('comments');
        Schema::drop('photos');
        Storage::fake('s3');

        $response = $this->actingAs($this->user)
            ->json('POST', route('photo.create'), ['photo' => UploadedFile::fake()->image('photo.jpg')]);
        $response->assertStatus(500);
        $this->assertEquals(0, count(Storage::cloud()->files()));
    }
}
