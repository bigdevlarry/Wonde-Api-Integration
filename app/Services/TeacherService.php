<?php

namespace App\Services;

use App\Contracts\SchoolInterface;
use App\Http\DTO\DailyScheduleDTO;
use App\Http\Traits\FormatsTime;
use App\Http\Traits\ExtractsLessonData;
use Illuminate\Support\Facades\Cache;

final readonly class TeacherService
{
    use FormatsTime, ExtractsLessonData;

    public function __construct(private SchoolInterface $school)
    {}

    public function getDailySchedule(string $teacherId, ?string $day = null): array
    {
        $cacheKey = "teacher_schedule_{$teacherId}_" . ($day ?? 'all');

        return Cache::remember($cacheKey, 1800, function () use ($teacherId, $day) {
            return $this->buildDailySchedule($teacherId, $day);
        });
    }

    private function buildDailySchedule(string $teacherId, ?string $day = null): array
    {
        $classes = $this->school->getTeacherClasses($teacherId);
        $classGroups = [];

        foreach ($classes as $class) {
            // First, get lessons to check if this class has lessons for the requested day
            $lessons = $this->school->getClassLessons($class->id);
            
            if (empty($lessons)) {
                // Only fetch students if no day filter or if we want classes without lessons
                if ($day === null) {
                    $students = $this->school->getClassStudents($class->id);
                    $classGroups[$class->id] = new DailyScheduleDTO(
                        className: $class->name,
                        classId: $class->id,
                        studentCount: count($students),
                        students: $students,
                        periods: []
                    );
                }
                continue;
            }

            $lessonsByDay = $this->groupLessonsByDay($lessons, $day);
            
            // Only process classes that have lessons for the requested day (or all days if no filter)
            if (!empty($lessonsByDay)) {
                // Now fetch students only for classes that match our criteria
                $students = $this->school->getClassStudents($class->id);
                
                foreach ($lessonsByDay as $lessonDay => $periods) {
                    $classGroups[$class->id . '_' . $lessonDay] = new DailyScheduleDTO(
                        className: $class->name,
                        classId: $class->id,
                        studentCount: count($students),
                        students: $students,
                        dayOfWeek: $lessonDay,
                        periods: $periods
                    );
                }
            }
        }

        return array_values($classGroups);
    }


    private function groupLessonsByDay(array $lessons, ?string $day): array
    {
        $lessonsByDay = [];
        
        foreach ($lessons as $lesson) {
            $lessonDay = $this->extractDayFromLesson($lesson);
            
            // If day filter is specified, only include lessons for that day
            if ($day !== null && $lessonDay !== $day) {
                continue;
            }

            if ($lessonDay) {
                $lessonsByDay[$lessonDay][] = [
                    'period' => $lesson->period->data->name ?? null,
                    'startTime' => $this->convertTimeToString($lesson->start_at ?? null),
                    'endTime' => $this->convertTimeToString($lesson->end_at ?? null)
                ];
            }
        }
        
        return $lessonsByDay;
    }

}