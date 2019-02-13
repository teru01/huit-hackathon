<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\ImageUpRequest;

class ImageUpController extends Controller
{
    public function confirm(ImageUpRequest $request) {
        $path = asset('storage/' . $request->myfile->store('logos', 'public'));
        DB::table('image')->insert(['image_path' => $path]);
        return ['path' => $path];
    }
}
