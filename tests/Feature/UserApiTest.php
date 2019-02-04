<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    public function test_ログイン中のユーザーを返却() {
        $response = $this->actingAs($this->user)->json('GET', route('user'));
        $response->assertStatus(200)->assertJson(['name' => $this->user->name]);
    }

    public function test_ログインされていない場合は空文字を返却() {
        $response = $this->json('GET', route('user'));
        $response->assertStatus(200);
        $this->assertEquals('', $response->content());
    }
}
