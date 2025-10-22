<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Helpers\AuthHelper;

class JobController extends Controller
{
    /**
     * GET /api/jobs
     * Público: lista de jobs (paginada).
     */
    public function index(Request $request)
    {
        $q = Job::query();

        // Filtros opcionales
        if ($request->filled('status')) {
            $q->where('status', (bool)$request->boolean('status'));
        }
        if ($request->filled('date')) {
            $q->whereDate('date', $request->input('date'));
        }

        $jobs = $q->orderByDesc('created_at')->paginate(15);

        // Si prefieres 404 cuando no hay registros, descomenta:
        // if ($jobs->isEmpty()) {
        //     return response()->json(['message' => 'Jobs aún no tiene datos registrados'], 404);
        // }

        return response()->json($jobs, 200);
    }

    /**
     * POST /api/job
     * Solo ADMIN.
     */
    public function store(Request $request)
    {
        // Autenticación vía JWT y verificación admin
        auth()->shouldUse('api');
        $user = auth('api')->user();
        if (!$user) return response()->json(['message' => 'No autenticado'], 401);
        if (!AuthHelper::isAdmin($user)) return response()->json(['message' => 'No autorizado'], 403);

        $validated = $request->validate([
            'latitude'  => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'date'      => ['required', 'date'],
            'time'      => ['required', 'string', 'max:10'],
            'notes'     => ['nullable', 'string'],
            'status'    => ['sometimes', 'boolean'],
        ]);

        $job = Job::create($validated);

        return response()->json($job, 201);
    }

    /**
     * GET /api/job/{id}
     * Público.
     */
    public function show($id)
    {
        $job = Job::find($id);
        if (!$job) {
            return response()->json(['message' => 'Job no encontrado'], 404);
        }
        return response()->json($job, 200);
    }

    /**
     * PUT /api/job/{id}
     * Solo ADMIN.
     */
    public function update(Request $request, $id)
    {
        auth()->shouldUse('api');
        $user = auth('api')->user();
        if (!$user) return response()->json(['message' => 'No autenticado'], 401);
        if (!AuthHelper::isAdmin($user)) return response()->json(['message' => 'No autorizado'], 403);

        $job = Job::find($id);
        if (!$job) {
            return response()->json(['message' => 'Job no encontrado'], 404);
        }

        $validated = $request->validate([
            'latitude'  => ['sometimes', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'numeric', 'between:-180,180'],
            'date'      => ['sometimes', 'date'],
            'time'      => ['sometimes', 'string', 'max:10'],
            'notes'     => ['sometimes', 'nullable', 'string'],
            'status'    => ['sometimes', 'boolean'],
        ]);

        $job->update($validated);

        return response()->json($job->fresh(), 200);
    }

    /**
     * DELETE /api/job/{id}
     * Solo ADMIN.
     */
    public function destroy($id)
    {
        auth()->shouldUse('api');
        $user = auth('api')->user();
        if (!$user) return response()->json(['message' => 'No autenticado'], 401);
        if (!AuthHelper::isAdmin($user)) return response()->json(['message' => 'No autorizado'], 403);

        $job = Job::find($id);
        if (!$job) {
            return response()->json(['message' => 'Job no encontrado'], 404);
        }

        $job->delete();
        return response()->json(null, 204);
    }
}