<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Auth\Request;

class RegisterApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_新しいユーザを作成して返却() {
        $data = [
            'name' => 'hoge',
            'email' => 'foo@gmail.com',
            'password' => 'test1234',
            'password_confirmation' => 'test1234',
        ];

        // jsonを指定のURLに送信
        $response = $this->json('POST', route('register'), $data);

        $user = User::first();
        // $user = DB::select('select * from users');
        $this->assertEquals($data['name'], $user->name);

        $response->assertStatus(201)->assertJson(['name' => $user->name]);
    }
}
