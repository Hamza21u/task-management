<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'task_id'          => ['required', 'integer', 'exists:tasks,task_id'],
            'project_list_id'  => ['sometimes', 'required', 'integer', 'exists:project_lists,project_list_id'],
            'task_title'       => ['sometimes', 'required', 'string', 'max:255'],
            'task_description' => ['nullable', 'string'],
        ];
    }
}
