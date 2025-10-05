<?php

namespace App\Http\Controllers\SchoolControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeacherStudentsRequest;
use App\Http\Traits\PaginatesData;
use App\Services\TeacherService;

class TeacherController extends Controller
{
    use PaginatesData;

    public function __construct(private readonly TeacherService $service)
    {}

    public function allStudents(TeacherStudentsRequest $request)
    {
        $validated = $request->validated();
        $teacherId = $validated['id'];
        $day = $validated['day'] ?? null;
        
        $dailySchedule = $this->service->getDailySchedule($teacherId, $day);
        
        $paginated = $this->paginateData($dailySchedule, $request);
        
        return response()->json($this->formatPaginatedResponse($paginated));
    }
}
