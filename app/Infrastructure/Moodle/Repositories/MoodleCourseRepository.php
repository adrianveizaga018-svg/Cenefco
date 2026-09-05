<?php

namespace App\Infrastructure\Moodle\Repositories;

use App\Domain\Moodle\Contracts\MoodleCourseRepositoryInterface;
use App\Infrastructure\Moodle\MoodleClient;

class MoodleCourseRepository implements MoodleCourseRepositoryInterface
{
    public function __construct(private readonly MoodleClient $client) {}

    public function create(array $data): array
    {
        $params = [
            'courses[0][fullname]' => $data['fullname'],
            'courses[0][shortname]' => $data['shortname'],
            'courses[0][categoryid]' => $data['categoryid'] ?? 1,
            'courses[0][summary]' => $data['summary'] ?? '',
            'courses[0][summaryformat]' => 1,
            'courses[0][format]' => $data['format'] ?? 'topics',
            'courses[0][visible]' => $data['visible'] ?? 1,
        ];

        if (! empty($data['startdate'])) {
            $params['courses[0][startdate]'] = $data['startdate'];
        }

        if (! empty($data['enddate'])) {
            $params['courses[0][enddate]'] = $data['enddate'];
        }

        $result = $this->client->call('core_course_create_courses', $params);
        $course = $result[0] ?? [];

        if (! empty($data['imageUrl']) && ! empty($course['id'])) {
            $this->uploadCourseImage($course['id'], $data['imageUrl']);
        }

        return $course;
    }

    private function uploadCourseImage(int $courseId, string $imageUrl): void
    {
        $imageContent = @file_get_contents($imageUrl);
        if (! $imageContent) {
            logger()->warning('MoodleClient: no se pudo descargar imagen', ['url' => $imageUrl]);

            return;
        }

        $tmpFile = tempnam(sys_get_temp_dir(), 'moodle_img_').'.jpg';
        file_put_contents($tmpFile, $imageContent);

        $moodleUrl = rtrim(config('services.moodle.url'), '/');
        $token = config('services.moodle.token');

        $ch = curl_init("{$moodleUrl}/webservice/upload.php");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_RESOLVE => ['moodle.local:80:127.0.0.1'],
            CURLOPT_POSTFIELDS => [
                'token' => $token,
                'filearea' => 'draft',
                'itemid' => 0,
                'filepath' => '/',
                'filename' => 'course_image.jpg',
                'file_1' => new \CURLFile($tmpFile, 'image/jpeg', 'course_image.jpg'),
            ],
        ]);
        $raw = curl_exec($ch);
        $curlErr = curl_error($ch);
        curl_close($ch);
        @unlink($tmpFile);

        $response = json_decode($raw, true);
        logger()->debug('MoodleClient upload', ['raw' => $raw, 'err' => $curlErr, 'response' => $response]);

        if (empty($response[0]['itemid'])) {
            return;
        }

        $this->client->call('core_course_update_courses', [
            'courses[0][id]' => $courseId,
            'courses[0][overviewfiles_itemid]' => $response[0]['itemid'],
        ]);
    }

    public function getAll(): array
    {
        return $this->client->call('core_course_get_courses') ?? [];
    }

    public function getById(int $id): array
    {
        $result = $this->client->call('core_course_get_courses', [
            'options[ids][0]' => $id,
        ]);

        return $result[0] ?? [];
    }

    public function delete(int $id): void
    {
        $this->client->call('core_course_delete_courses', [
            'courseids[0]' => $id,
        ]);
    }
}
