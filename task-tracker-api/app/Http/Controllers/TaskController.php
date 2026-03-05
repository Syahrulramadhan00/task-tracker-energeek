<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/tasks",
     * tags={"Tasks"},
     * summary="List semua task (bisa difilter per project)",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="project_id", in="query", description="Filter berdasarkan ID Project", @OA\Schema(type="integer")),
     * @OA\Parameter(name="search", in="query", description="Cari task berdasarkan judul", @OA\Schema(type="string")),
     * @OA\Response(response=200, description="Daftar task berhasil diambil")
     * )
     */

    /**
     * @OA\Post(
     * path="/api/tasks",
     * tags={"Tasks"},
     * summary="Tambah task ke dalam project",
     * security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"project_id","category_id","title","description","due_date"},
     * @OA\Property(property="project_id", type="integer", example=1),
     * @OA\Property(property="category_id", type="integer", example=1),
     * @OA\Property(property="title", type="string", example="Setup CI/CD"),
     * @OA\Property(property="description", type="string", example="Konfigurasi GitHub Actions"),
     * @OA\Property(property="due_date", type="string", format="date", example="2025-03-10")
     * )
     * ),
     * @OA\Response(response=201, description="Task berhasil dibuat")
     * )
     */

    /**
     * @OA\Delete(
     * path="/api/tasks/{id}",
     * tags={"Tasks"},
     * summary="Hapus task (Soft Delete)",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="Task berhasil dihapus secara soft delete")
     * )
     */
    public function index(Request $request)
    {
        $query = Task::with(['category', 'project']);

        // Filter by Project (Read list task per project) [cite: 375]
        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Searching Task by judul [cite: 379]
        if ($request->has('search')) {
            $query->where('title', 'ilike', '%' . $request->search . '%');
        }

        return response()->json($query->orderBy('due_date', 'asc')->get());
    }

    public function store(Request $request)
    {
        // Semua field task bisa diubah & wajib diisi kecuali dinyatakan lain [cite: 376, 403]
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'category_id' => 'required|exists:categories,id', // Kategori menggunakan dropdown yang datanya berasal dari tabel categories [cite: 407]
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'required|date',
        ]);

        $validated['created_by'] = Auth::id();
        $task = Task::create($validated);

        return response()->json(['status' => 'success', 'message' => 'Task created successfully.', 'data' => $task], 201);
    }

    public function show(Task $task)
    {
        return response()->json($task->load(['category', 'project']));
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'required|date',
        ]);

        $task->update($validated);

        return response()->json(['status' => 'success', 'message' => 'Task updated successfully.', 'data' => $task]);
    }

    public function destroy(Task $task)
    {
        // Soft delete pada task field deleted_at dan deleted_by terisi saat dihapus [cite: 409]
        $task->update(['deleted_by' => Auth::id()]);
        $task->delete(); // Memicu soft delete dari Laravel (mengisi deleted_at)

        return response()->json(['status' => 'success', 'message' => 'Task deleted successfully.']);
    }
}