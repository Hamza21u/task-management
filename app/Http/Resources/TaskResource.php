<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'task_id'          => $this->task_id,
            'project_id'       => $this->project_id,
            'project_list_id'  => $this->project_list_id,
            'task_title'       => $this->task_title,
            'task_description' => $this->task_description,
        ];
    }
}
