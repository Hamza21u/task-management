<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreProjectListRequest;
use App\Http\Resources\ProjectListItemResource;
use App\Models\Project;
use App\Models\ProjectList;
use Illuminate\Support\Facades\Auth;

class ProjectListController extends Controller
{
    public function create(StoreProjectListRequest $request)
    {
        $project = Project::find($request->project_id);

        if ($project && $project->user_id === Auth::user()->id) {
            $projectList = ProjectList::create([
                'project_id' => $project->project_id,
                'list_name'  => $request->list_name,
            ]);
            return new ProjectListItemResource($projectList);
        } else {
            return response()->json(['error' => 'Project not found or unauthorized'], 404);
        }
    }

    //update
    public function update(Request $request, $project, $list)
    {
        $project = Project::where('slug', $project)->first();

        if ($project && $project->user_id === Auth::user()->id) {
            $projectList = ProjectList::where('project_list_id', $list)
                ->where('project_id', $project->project_id)
                ->first();

            if ($projectList) {
                $projectList->update([
                    'list_name' => $request->list_name,
                ]);
                return new ProjectListItemResource($projectList);
            } else {
                return response()->json(['error' => 'Project list not found'], 404);
            }
        } else {
            return response()->json(['error' => 'Project not found or unauthorized'], 404);
        }
    }

    //destroy
    public function destroy(Request $request, $project, $list)
    {
        $project = Project::where('slug', $project)->first();

        if ($project && $project->user_id === Auth::user()->id) {
            $projectList = ProjectList::where('project_list_id', $list)
                ->where('project_id', $project->project_id)
                ->first();

            if ($projectList) {
                $projectList->delete();
                return response()->json(['message' => 'Project list deleted successfully']);
            } else {
                return response()->json(['error' => 'Project list not found'], 404);
            }
        } else {
            return response()->json(['error' => 'Project not found or unauthorized'], 404);
        }
    }
}
