<?php

namespace App\Services;

use App\Contracts\SchoolInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Wonde\Client;
use Exception;

final readonly class WondeService implements SchoolInterface
{
    private string $schoolId;
    private Client $client;

    /**
     * @throws \Wonde\Exceptions\InvalidTokenException
     */
    public function __construct()
    {
        $this->client = new Client(Config::get('wonde.access_token'));
        $this->schoolId = Config::get('wonde.school_id');
    }

    /**
     * @throws \Exception
     */
    public function getTeacherClasses(string $teacherId): array
    {
        try {
            $response = $this->client->school($this->schoolId)
                ->employees
                ->get($teacherId, ['include' => 'classes']);

            return $response->classes->data ?? [];
        } catch (Exception $e) {
            Log::error(
                sprintf('Wonde API error fetching teacher classes for teacher_id: %s', $teacherId), [
                'error' => $e->getMessage()
            ]);
            throw new Exception('Failed to fetch teacher classes', 0, $e);
        }
    }

    /**
     * @throws \Exception
     */
    public function getClassStudents(string $classId): array
    {
        try {
            $response = $this->client->school($this->schoolId)
                ->classes
                ->get($classId, ['include' => 'students']);

            return $response->students->data ?? [];
        } catch (Exception $e) {
            Log::error(
                sprintf('Wonde API error fetching class students for class_id: %s', $classId), [
                'error' => $e->getMessage()
            ]);
            throw new Exception('Failed to fetch class students', 0, $e);
        }
    }

    /**
     * @throws \Exception
     */
    public function getClassLessons(string $classId): array
    {
        try {
            $response = $this->client->school($this->schoolId)
                ->classes
                ->get($classId, ['include' => 'lessons,lessons.period']);

            return $response->lessons->data ?? [];
        } catch (Exception $e) {
            Log::error(
                sprintf('Wonde API error fetching class lessons for class_id: %s', $classId), [
                'error' => $e->getMessage()
            ]);
            throw new Exception('Failed to fetch class lessons', 0, $e);
        }
    }
}