<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeacherStudentsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|string|regex:/^A\d+$/',
            'day' => 'nullable|string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('id'),
            'day' => $this->normalizeDay($this->input('day')),
        ]);
    }

    /**
     * Normalize day input to lowercase
     */
    private function normalizeDay(?string $day): ?string
    {
        if ($day === null) {
            return null;
        }

        return strtolower(trim($day));
    }

    public function messages(): array
    {
        return [
            'id.required' => 'Teacher ID is required.',
            'id.string' => 'Teacher ID must be a string.',
            'id.regex' => 'Teacher ID must be a valid Wonde employee ID format (e.g., A123456790).',
            'day.string' => 'Day must be a string.',
            'day.in' => 'Day must be one of: monday, tuesday, wednesday, thursday, friday, saturday, sunday.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'id' => 'teacher ID',
        ];
    }
}
