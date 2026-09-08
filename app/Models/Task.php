<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $primaryKey = 'task_id';

    protected $fillable = [
        'project_id',
        'project_list_id',
        'task_title',
        'task_description',
    ];
}
