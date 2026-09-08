<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function create(StoreTaskRequest $request, $project)
    {
        $project = Project::where('slug', $project)->first();

        if ($project && $project->user_id === Auth::user()->id) {
            $task = Task::create([
                'project_id'       => $project->project_id,
                'project_list_id'  => $request->project_list_id,
                'task_title'       => $request->task_title,
                'task_description' => $request->task_description,
            ]);
            return new TaskResource($task);
        } else {
            return response()->json(['error' => 'Project not found or unauthorized'], 404);
        }
    }

    //update
    public function update(Request $request, $project, $task)
    {
        $project = Project::where('slug', $project)->first();

        if ($project && $project->user_id === Auth::user()->id) {
            $task = Task::where('task_id', $task)
                ->where('project_id', $project->project_id)
                ->first();

            if ($task) {
                $task->update([
                    'project_list_id'  => $request->project_list_id ?? $task->project_list_id,
                    'task_title'       => $request->task_title ?? $task->task_title,
                    'task_description' => $request->task_description ?? $task->task_description,
                ]);
                return new TaskResource($task);
            } else {
                return response()->json(['error' => 'Task not found'], 404);
            }
        } else {
            return response()->json(['error' => 'Project not found or unauthorized'], 404);
        }
    }

    //destroy
    public function destroy(Request $request, $project, $task)
    {
        $project = Project::where('slug', $project)->first();

        if ($project && $project->user_id === Auth::user()->id) {
            $task = Task::where('task_id', $task)
                ->where('project_id', $project->project_id)
                ->first();

            if ($task) {
                $task->delete();
                return response()->json(['message' => 'Task deleted successfully']);
            } else {
                return response()->json(['error' => 'Task not found'], 404);
            }
        } else {
            return response()->json(['error' => 'Project not found or unauthorized'], 404);
        }
    }
}
