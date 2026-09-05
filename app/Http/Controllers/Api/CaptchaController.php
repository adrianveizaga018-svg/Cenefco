<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Mews\Captcha\Facades\Captcha;

class CaptchaController extends Controller
{
    public function generate(): JsonResponse
    {
        $captcha = Captcha::create('inscripcion', true);

        return response()->json([
            'key' => $captcha['key'],
            'img' => $captcha['img'],
        ]);
    }
}
