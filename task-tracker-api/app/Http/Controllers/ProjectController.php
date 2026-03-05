<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/projects",
     * tags={"Projects"},
     * summary="List semua project",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="search", in="query", description="Cari project berdasarkan nama", @OA\Schema(type="string")),
     * @OA\Response(response=200, description="Daftar project berhasil diambil")
     * )
     */

    /**
     * @OA\Post(
     * path="/api/projects",
     * tags={"Projects"},
     * summary="Tambah project baru",
     * security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name","description","status"},
     * @OA\Property(property="name", type="string", example="Website E-Commerce"),
     * @OA\Property(property="description", type="string", example="Redesign platform belanja online"),
     * @OA\Property(property="status", type="string", enum={"active", "archived"}, example="active")
     * )
     * ),
     * @OA\Response(response=201, description="Project berhasil dibuat"),
     * @OA\Response(response=422, description="Validasi gagal")
     * )
     */

    /**
     * @OA\Get(
     * path="/api/projects/{id}",
     * tags={"Projects"},
     * summary="Detail project beserta list task",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="Detail project berhasil diambil")
     * )
     */

    /**
     * @OA\Put(
     * path="/api/projects/{id}",
     * tags={"Projects"},
     * summary="Update informasi project",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * @OA\Property(property="name", type="string"),
     * @OA\Property(property="description", type="string"),
     * @OA\Property(property="status", type="string", enum={"active", "archived"})
     * )
     * ),
     * @OA\Response(response=200, description="Project berhasil diupdate")
     * )
     */
    public function index(Request $request)
    {
        $query = Project::query();

        // Fitur Searching Project by judul 
        if ($request->has('search')) {
            $query->where('name', 'ilike', '%' . $request->search . '%');
        }

        return response()->json($query->orderBy('created_at', 'desc')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:active,archived'
        ], [
            'name.required'        => 'Project name is required.',
            'description.required' => 'Project description is required.',
            'status.in'            => 'Status must be either active or archived.',
        ]);

        $validated['created_by'] = Auth::id();
        $project = Project::create($validated);

        return response()->json(['status' => 'success', 'message' => 'Project created successfully.', 'data' => $project], 201);
    }

    public function show($id)
    {
        // Pada project detail, tampilkan list task [cite: 367]
        $project = Project::with('tasks.category')->findOrFail($id);
        return response()->json($project);
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:active,archived' // Di menu project tidak ada deleted, yang ada hanya update status [cite: 408]
        ]);

        $project->update($validated);

        return response()->json(['status' => 'success', 'message' => 'Project updated successfully.', 'data' => $project]);
    }
}