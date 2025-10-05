<?php

namespace App\Contracts;

interface SchoolInterface
{
    public function getTeacherClasses(string $teacherId): array;
    public function getClassStudents(string $classId): array;
    public function getClassLessons(string $classId): array;
}
