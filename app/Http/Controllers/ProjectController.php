<?php

namespace App\Http\Controllers;



use Illuminate\Http\Request;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Resources\ProjectListResource;
use Illuminate\Support\Str;
use App\Models\Project;
//auth
use Illuminate\Support\Facades\Auth;


class ProjectController extends Controller
{
    /**
     * List all projects belonging to the authenticated user.
     */
    public function list(Request $request)
    {
        $projects = Project::where('user_id',Auth::user()->id)->get();
        return  ProjectListResource::collection($projects);
    }

    public function create(StoreProjectRequest $request)
    {
       $project=  Project::create([
            'user_id' =>Auth::user()->id,
            'project_name' => $request->name,
            'slug' => $this->generateSlug($request->name),
        ]);
        return new ProjectListResource($project);
    }

    //update
    public function update(Request $request)
    {
        $project = Project::where('slug', $request->slug)->first();
        if($project && $project->user_id ===Auth::user()->id){
            $project->update([
                'project_name' => $request->name,
                'slug' => $this->generateSlug($request->name),
            ]);
            return new ProjectListResource($project);
        }else{
            return response()->json(['error' => 'Project not found or unauthorized'], 404); 
        }
       
    }


    //destroy
    public function destroy(Request $request)
    {
        $project = Project::where('slug', $request->slug)->first();
        if($project && $project->user_id ===Auth::user()->id){
            $project->delete();
            return response()->json(['message' => 'Project deleted successfully']);
        }else{
            return response()->json(['error' => 'Project not found or unauthorized'], 404);
        }
    }




    private function generateSlug($name)
    {
        $slug = Str::slug($name);
        $count = Project::where('slug', 'like', "{$slug}%")->count();
        return $count ? "{$slug}-{$count}" : $slug;
    }


       
   
}
