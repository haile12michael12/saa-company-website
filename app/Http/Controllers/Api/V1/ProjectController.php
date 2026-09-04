<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user()?->company_id;
        $projects = Project::when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->with(['customer'])
            ->latest()
            ->paginate($request->get('per_page', 15));

        return response()->json($projects);
    }

    public function show(Project $project)
    {
        return response()->json($project->load(['customer', 'milestones', 'tasks', 'files', 'updates']));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'status' => 'sometimes|required|string|in:planning,in_progress,on_hold,completed,cancelled',
            'progress' => 'nullable|integer|between:0,100',
            'description' => 'nullable|string',
        ]);

        $project->update($validated);

        return response()->json($project);
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return response()->json(['message' => 'Project deleted.']);
    }
}