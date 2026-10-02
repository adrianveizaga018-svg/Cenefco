<?php

namespace App\Infrastructure\Moodle;

use RuntimeException;

class MoodleClient
{
    private string $url;

    private string $token;

    public function __construct()
    {
        $this->url = rtrim(config('services.moodle.url'), '/');
        $this->token = config('services.moodle.token');
    }

    public function call(string $function, array $params = []): mixed
    {
        $url = "{$this->url}/webservice/rest/server.php"
            ."?wstoken={$this->token}"
            ."&wsfunction={$function}"
            .'&moodlewsrestformat=json';

        foreach ($params as $key => $value) {
            $url .= '&'.$key.'='.urlencode((string) $value);
        }

        $body = file_get_contents($url);

        logger()->debug('MoodleClient', ['url' => $url, 'body' => $body]);

        $data = json_decode($body, true);

        if (isset($data['exception'])) {
            throw new RuntimeException("[Moodle] {$data['message']} ({$data['exception']})");
        }

        return $data;
    }
}
