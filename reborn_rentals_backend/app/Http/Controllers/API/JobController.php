<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Helpers\AuthHelper;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *   schema="Job",
 *   @OA\Property(property="id", type="integer", example=12),
 *   @OA\Property(property="latitude", type="number", format="float", example=-17.383),
 *   @OA\Property(property="longitude", type="number", format="float", example=-66.145),
 *   @OA\Property(property="date", type="string", format="date", example="2025-10-22"),
 *   @OA\Property(property="time", type="string", example="09:30"),
 *   @OA\Property(property="notes", type="string", nullable=true, example="Revisión de equipo en sitio."),
 *   @OA\Property(property="status", type="boolean", example=true),
 *   @OA\Property(property="created_at", type="string", format="date-time"),
 *   @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Tag(
 *   name="Jobs",
 *   description="Gestión de trabajos o tareas geolocalizadas"
 * )
 */
class JobController extends Controller
{
    /**
     * GET /api/jobs
     * Público: lista de jobs (paginada).
     *
     * @OA\Get(
     *   path="/api/jobs",
     *   tags={"Jobs"},
     *   summary="Listar trabajos (público, con filtros opcionales)",
     *   @OA\Parameter(
     *     name="status", in="query", required=false,
     *     description="Filtrar por estado (true/false)",
     *     @OA\Schema(type="boolean")
     *   ),
     *   @OA\Parameter(
     *     name="date", in="query", required=false,
     *     description="Filtrar por fecha exacta (YYYY-MM-DD)",
     *     @OA\Schema(type="string", format="date")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Listado paginado de jobs",
     *     @OA\JsonContent(
     *       @OA\Property(property="current_page", type="integer", example=1),
     *       @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Job")),
     *       @OA\Property(property="total", type="integer", example=12),
     *       @OA\Property(property="per_page", type="integer", example=15)
     *     )
     *   )
     * )
     */
    public function index(Request $request)
    {
        $q = Job::query();

        if ($request->filled('status')) {
            $q->where('status', (bool)$request->boolean('status'));
        }
        if ($request->filled('date')) {
            $q->whereDate('date', $request->input('date'));
        }

        $jobs = $q->orderByDesc('created_at')->paginate(15);
        return response()->json($jobs, 200);
    }

    /**
     * POST /api/job
     * Solo ADMIN.
     *
     * @OA\Post(
     *   path="/api/job",
     *   tags={"Jobs"},
     *   summary="Crear trabajo (solo admin)",
     *   security={{"bearerAuth":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"latitude","longitude","date","time"},
     *       @OA\Property(property="latitude", type="number", format="float", example=-17.383),
     *       @OA\Property(property="longitude", type="number", format="float", example=-66.145),
     *       @OA\Property(property="date", type="string", format="date", example="2025-10-22"),
     *       @OA\Property(property="time", type="string", example="09:30"),
     *       @OA\Property(property="notes", type="string", nullable=true, example="Revisión técnica programada"),
     *       @OA\Property(property="status", type="boolean", example=true)
     *     )
     *   ),
     *   @OA\Response(response=201, description="Creado", @OA\JsonContent(ref="#/components/schemas/Job")),
     *   @OA\Response(response=401, description="No autenticado"),
     *   @OA\Response(response=403, description="No autorizado"),
     *   @OA\Response(response=422, description="Error de validación")
     * )
     */
    public function store(Request $request)
    {
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
     *
     * @OA\Get(
     *   path="/api/job/{id}",
     *   tags={"Jobs"},
     *   summary="Obtener detalle de un trabajo (público)",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=12),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/Job")),
     *   @OA\Response(response=404, description="Job no encontrado")
     * )
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
     *
     * @OA\Put(
     *   path="/api/job/{id}",
     *   tags={"Jobs"},
     *   summary="Actualizar trabajo (solo admin)",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=12),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       @OA\Property(property="latitude", type="number", format="float", example=-17.4),
     *       @OA\Property(property="longitude", type="number", format="float", example=-66.18),
     *       @OA\Property(property="date", type="string", format="date", example="2025-10-23"),
     *       @OA\Property(property="time", type="string", example="14:00"),
     *       @OA\Property(property="notes", type="string", example="Cambio de locación y hora"),
     *       @OA\Property(property="status", type="boolean", example=false)
     *     )
     *   ),
     *   @OA\Response(response=200, description="Actualizado", @OA\JsonContent(ref="#/components/schemas/Job")),
     *   @OA\Response(response=401, description="No autenticado"),
     *   @OA\Response(response=403, description="No autorizado"),
     *   @OA\Response(response=404, description="Job no encontrado"),
     *   @OA\Response(response=422, description="Error de validación")
     * )
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
     *
     * @OA\Delete(
     *   path="/api/job/{id}",
     *   tags={"Jobs"},
     *   summary="Eliminar trabajo (solo admin)",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=12),
     *   @OA\Response(response=204, description="Eliminado"),
     *   @OA\Response(response=401, description="No autenticado"),
     *   @OA\Response(response=403, description="No autorizado"),
     *   @OA\Response(response=404, description="Job no encontrado")
     * )
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