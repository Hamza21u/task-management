<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreProjectListRequest;
use App\Http\Resources\ProjectListItemResource;

class ProjectListController extends Controller
{
    public function create(StoreProjectListRequest $request)
    {
        // Implementation for creating a project list
        $projectList = ProjectList::create([
            'project_id' => $request->project_id,
            'list_name' => $request->list_name,
        ]);
        return new ProjectListItemResource($projectList);
    }

    public function update(Request $request)
    {
        // Implementation for updating a project list
        $projectList = ProjectList::find($request->id);
        if ($projectList) {
            $projectList->update([
                'list_name' => $request->list_name,
            ]);
            return new ProjectListItemResource($projectList);
        } else {
            return response()->json(['error' => 'Project list not found'], 404);
        }
    }
    public function destroy(Request $request)
    {
        // Implementation for deleting a project list
        $projectList = ProjectList::find($request->id);
        if ($projectList) {
            $projectList->delete();
            return response()->json(['message' => 'Project list deleted successfully']);
        } else {
            return response()->json(['error' => 'Project list not found'], 404);
        }
    }
}
