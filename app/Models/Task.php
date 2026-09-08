<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $primaryKey = 'task_id';

    protected $fillable = [
        'project_list_id',
        'task_title',
        'task_description',
    ];

    public function list(): BelongsTo
    {
        return $this->belongsTo(ProjectList::class, 'project_list_id', 'project_list_id');
    }
}
