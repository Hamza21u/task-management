<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectList extends Model
{
    protected $primaryKey = 'project_list_id'; // Specify the primary key column name
    protected $fillable = [
        'project_id',
        'list_name'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'project_list_id', 'project_list_id');
    }
}
