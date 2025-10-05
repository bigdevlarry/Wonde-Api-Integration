<?php

namespace App\Http\Traits;

trait FormatsTime
{
    /**
     * Convert various time formats to user-friendly 12-hour format (g:i A)
     */
    protected function convertTimeToString($time): ?string
    {
        if ($time === null) {
            return null;
        }

        // Extract time string from different formats
        if (is_string($time)) {
            $timeString = $time;
        } elseif (is_object($time) && isset($time->date)) {
            $timeString = $time->date;
        } else {
            $timeString = (string) $time;
        }
        
        try {
            $dateTime = new \DateTime($timeString);
            return $dateTime->format('g:i A');
        } catch (\Exception $e) {
            return $timeString;
        }
    }
}
