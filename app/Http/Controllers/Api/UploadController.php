<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Upload\UploadFileRequest;
use App\Http\Requests\Upload\UploadImageRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class UploadController extends Controller
{
    public function image(UploadImageRequest $request): JsonResponse
    {
        $file     = $request->file('file');
        $filename = Str::uuid().'.webp';
        $folder   = 'uploads/images';

        $manager  = new ImageManager(new Driver);
        $webp     = (string) $manager->read($file->getRealPath())
            ->scaleDown(width: 1920, height: 1920)
            ->toWebp(quality: 82);

        Storage::disk('public')->put("{$folder}/{$filename}", $webp);

        return response()->json(['url' => "/storage/{$folder}/{$filename}"], 201);
    }

    public function file(UploadFileRequest $request): JsonResponse
    {
        $file = $request->file('file');
        $path = $file->storeAs('uploads/files', Str::uuid().'.'.$file->getClientOriginalExtension(), 'public');

        return response()->json(['url' => '/storage/'.$path], 201);
    }
}
