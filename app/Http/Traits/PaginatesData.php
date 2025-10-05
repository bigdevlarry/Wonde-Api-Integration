<?php

namespace App\Http\Traits;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

trait PaginatesData
{
    /**
     * Paginate an array of data
     */
    protected function paginateData(array $data, Request $request, int $perPage = 10): LengthAwarePaginator
    {
        $currentPage = $request->get('page', 1);
        
        $queryParams = $request->query();
        $routeParams = $request->route() ? $request->route()->parameters() : [];
        $filteredQuery = array_diff_key($queryParams, $routeParams);
        
        return new LengthAwarePaginator(
            collect($data)->forPage($currentPage, $perPage),
            count($data),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $filteredQuery
            ]
        );
    }

    /**
     * Format paginated response
     */
    protected function formatPaginatedResponse(LengthAwarePaginator $paginated): array
    {
        return [
            'status' => 'success',
            'data' => $paginated->items(),
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'last_page' => $paginated->lastPage(),
                'has_more_pages' => $paginated->hasMorePages(),
                'next_page_url' => $paginated->nextPageUrl(),
                'prev_page_url' => $paginated->previousPageUrl(),
                'first_page_url' => $paginated->url(1),
                'last_page_url' => $paginated->url($paginated->lastPage())
            ]
        ];
    }
}
