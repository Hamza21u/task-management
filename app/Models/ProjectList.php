<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectList extends Model
{
    protected $primaryKey = 'project_list_id'; // Specify the primary key column name
    protected $fillable = [
        'project_id',
        'list_name'
    ];
}
