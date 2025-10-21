<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jobs = Job::all();
        if (!$jobs) {
            return response()->json(['message' => 'Jobs aún no tiene datos registrados'], 404);
        }   
        return response()->json($jobs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'latitude'  => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'date'      => ['required', 'date'],
            'time'      => ['required', 'string', 'max:10'],
            'notes'     => ['nullable', 'string'],
            'status'    => ['boolean'],
        ]);

        $job = Job::create($validated);

        return response()->json($job, 201);
    }

    /**
     * Display the specified resource.
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $job = Job::find($id);
        if (!$job) {
            return response()->json(['message' => 'Job no encontrado'], 404);
        }

        $validated = $request->validate([
            'latitude'  => ['sometimes', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'numeric', 'between:-180,180'],
            'date'      => ['sometimes', 'date'],
            'time'      => ['sometimes', 'string', 'max:10'],
            'notes'     => ['nullable', 'string'],
            'status'    => ['boolean'],
        ]);

        $job->update($validated);

        return response()->json($job, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $job = Job::find($id);
        if (!$job) {
            return response()->json(['message' => 'Job no encontrado'], 404);
        }

        $job->delete();
        return response()->json(['message' => 'Job eliminado correctamente'], 200);   
    }
}