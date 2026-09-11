<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$token = env('META_ACCESS_TOKEN');
$accountId = 'act_1652384699141172';

$response = Illuminate\Support\Facades\Http::withoutVerifying()
    ->timeout(30)
    ->withOptions(['curl' => [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4]])
    ->get("https://graph.facebook.com/v19.0/{$accountId}/campaigns", [
        'access_token' => $token,
        'fields' => 'id,name,status,insights{spend,actions}',
        'limit' => 25
    ]);

foreach ($response->json('data') ?? [] as $camp) {
    $actions = $camp['insights']['data'][0]['actions'] ?? [];
    foreach ($actions as $act) {
        $type = $act['action_type'];
        if (str_contains($type, 'lead') || str_contains($type, 'contact') || str_contains($type, 'conversation')) {
            echo "Campaña [{$camp['name']}] -> {$type} = {$act['value']}\n";
        }
    }
}