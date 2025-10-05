<?php

namespace App\Http\DTO;

final readonly class DailyScheduleDTO
{
    public function __construct(
        public string $className,
        public string $classId,
        public int $studentCount,
        public array $students,
        public ?string $dayOfWeek = null,
        public array $periods = []
    ) {}
}
