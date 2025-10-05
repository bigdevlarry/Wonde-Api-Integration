<?php

namespace App\Http\Traits;

trait ExtractsLessonData
{
    /**
     * Extract day from lesson data structure
     */
    protected function extractDayFromLesson($lesson): ?string
    {
        // Try to get day from period data structure
        if (isset($lesson->period->data->day)) {
            return strtolower(trim($lesson->period->data->day));
        }
        
        // Try to get day from lesson directly
        if (isset($lesson->day)) {
            return strtolower(trim($lesson->day));
        }

        return null;
    }
}
