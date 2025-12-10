<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Project;

class SetActiveProject
{
    public function handle(Request $request, Closure $next)
    {
        $project = $request->route('project');

        if ($project instanceof Project) {
            session([
                'active_project_id' => $project->id,
                'active_project_name' => $project->name,
                'active_project_start_date' => $project->start_date,
                'active_project_status' => $project->status,
                'active_project_slug' => $project->slug,
                'active_project_data' => $project,
            ]);
        }

        return $next($request);
    }
}
