<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;

class LogoutApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    public function test_認証済みユーザーをログアウトさせる() {
        $response = $this->actingAs($this->user)->json('POST', route('logout'));
        $response->assertStatus(200);
        $this->assertGuest();
    }
}
