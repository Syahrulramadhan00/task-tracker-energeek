<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/dashboard",
     * tags={"Dashboard"},
     * summary="Statistik dashboard",
     * security={{"bearerAuth":{}}},
     * @OA\Response(
     * response=200,
     * description="Data dashboard berhasil diambil",
     * @OA\JsonContent(
     * @OA\Property(property="active_projects_count", type="integer", example=8),
     * @OA\Property(property="incomplete_tasks_count", type="integer", example=44),
     * @OA\Property(property="upcoming_tasks", type="array", @OA\Items(type="object"))
     * )
     * )
     * )
     */
    public function index()
    {
        // 1. Total project aktif [cite: 381]
        $activeProjectsCount = Project::where('status', 'active')->count();

        // 2. Total task belum selesai (Asumsi selain kategori 'DONE') [cite: 382]
        $incompleteTasksCount = Task::whereHas('category', function ($query) {
            $query->where('name', '!=', 'DONE');
        })->count();

        // 3. Daftar task yang mendekati due date (Task belum selesai & due date paling dekat) [cite: 383]
        $upcomingTasks = Task::with('project')
            ->whereHas('category', function ($query) {
                $query->where('name', '!=', 'DONE');
            })
            ->where('due_date', '>=', now())
            ->orderBy('due_date', 'asc')
            ->take(5) // Ambil 5 teratas
            ->get();

        return response()->json([
            'active_projects_count' => $activeProjectsCount,
            'incomplete_tasks_count' => $incompleteTasksCount,
            'upcoming_tasks' => $upcomingTasks
        ]);
    }
}
