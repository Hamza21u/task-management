<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\ProjectList;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Create a new task under a specific list.
     * Route: POST /api/tasks
     */
    public function create(StoreTaskRequest $request)
    {
        // Find list belonging to a project owned by the authenticated user
        $projectList = ProjectList::where('project_list_id', $request->project_list_id)
            ->whereHas('project', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->first();

        if (!$projectList) {
            return response()->json(['error' => 'Project list not found or unauthorized'], 404);
        }

        $task = $projectList->tasks()->create([
            'task_title'       => $request->task_title,
            'task_description' => $request->task_description,
        ]);

        return (new TaskResource($task))->response()->setStatusCode(201);
    }

    /**
     * Update an existing task.
     * Route: POST /api/tasks/update
     */
    public function update(UpdateTaskRequest $request)
    {
        // Locate task belonging to a project owned by the authenticated user
        $taskModel = Task::where('task_id', $request->task_id)
            ->whereHas('list.project', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->first();

        if (!$taskModel) {
            return response()->json(['error' => 'Task not found or unauthorized'], 404);
        }

        // If moving task to another list, verify target list also belongs to the authenticated user
        if ($request->filled('project_list_id')) {
            $targetList = ProjectList::where('project_list_id', $request->project_list_id)
                ->whereHas('project', function ($query) {
                    $query->where('user_id', Auth::id());
                })
                ->first();

            if (!$targetList) {
                return response()->json(['error' => 'Target project list not found or unauthorized'], 422);
            }
        }

        $taskModel->update($request->safe()->except('task_id'));

        return new TaskResource($taskModel);
    }

    /**
     * Delete an existing task.
     * Route: DELETE /api/tasks/destroy
     */
    public function destroy(Request $request): JsonResponse
    {
        // Locate task belonging to a project owned by the authenticated user
        $taskModel = Task::where('task_id', $request->task_id)
            ->whereHas('list.project', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->first();

        if (!$taskModel) {
            return response()->json(['error' => 'Task not found or unauthorized'], 404);
        }

        $taskModel->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }
}
