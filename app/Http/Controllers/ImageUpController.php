<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ImageUpRequest;

class ImageUpController extends Controller
{
    public function confirm(ImageUpRequest $request) {
        return response(null, 200);
    }
}
