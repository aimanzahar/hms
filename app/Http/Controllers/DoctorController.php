<?php

namespace App\Http\Controllers;

use App\Http\Requests\DoctorProfileUpdateRequest;
use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\View;

class DoctorController extends Controller
{
    /**
     * Display a listing of doctors for patients to browse.
     */
    public function index(Request $request): ViewContract|JsonResponse
    {
        $query = Doctor::query()
            ->with([
                'user:id,name,email',
            ])
            ->withCount('appointments')
            ->orderByDesc('appointments_count')
            ->orderBy('specialization');

        if ($request->filled('specialization')) {
            $query->where('specialization', 'like', '%'.$request->string('specialization')->trim().'%');
        }

        $doctors = $query->paginate(15)->withQueryString();

        return $this->respond($request, 'doctor.index', [
            'doctors' => $doctors,
        ]);
    }

    /**
     * Display the specified doctor profile.
     */
    public function show(Request $request, Doctor $doctor): ViewContract|JsonResponse
    {
        $doctor->load([
            'user:id,name,email',
            'appointments' => function ($query) {
                $query->latest('appointment_date')
                    ->take(5)
                    ->with([
                        'patient.user:id,name,email',
                    ]);
            },
        ]);

        return $this->respond($request, 'doctor.show', [
            'doctor' => $doctor,
        ]);
    }

    /**
     * Display the authenticated doctor's own profile.
     */
    public function profile(Request $request): ViewContract|JsonResponse
    {
        $user = $request->user();

        abort_unless($user && $user->isDoctor(), 403);

        $doctor = $user->doctor()
            ->with([
                'appointments' => function ($query) {
                    $query->where('appointment_date', '>=', Carbon::now()->startOfDay())
                        ->orderBy('appointment_date')
                        ->take(5)
                        ->with('patient.user:id,name,email');
                },
            ])
            ->first();

        $statistics = [
            'total_appointments' => Appointment::query()
                ->where('doctor_id', optional($user->doctor)->id)
                ->count(),
            'upcoming_appointments' => Appointment::query()
                ->where('doctor_id', optional($user->doctor)->id)
                ->where('appointment_date', '>=', Carbon::now())
                ->count(),
            'unique_patients' => Appointment::query()
                ->where('doctor_id', optional($user->doctor)->id)
                ->distinct()
                ->count('patient_id'),
        ];

        return $this->respond($request, 'doctor.profile', [
            'doctor' => $doctor,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Update the authenticated doctor's profile.
     */
    public function updateProfile(DoctorProfileUpdateRequest $request): RedirectResponse|JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $doctor = $user->doctor()->firstOrNew();

        if (! $doctor->exists) {
            $doctor->user()->associate($user);
        }

        $doctor->fill($validated);
        $doctor->save();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => __('Doctor profile updated successfully.'),
                'doctor' => $doctor->load('user:id,name,email'),
            ]);
        }

        return redirect()
            ->route('doctor.profile')
            ->with('status', __('Doctor profile updated successfully.'));
    }

    /**
     * Helper to respond with JSON when appropriate or render a view when available.
     *
     * @return ViewContract|JsonResponse|Application|Response
     */
    protected function respond(Request $request, string $view, array $data): ViewContract|JsonResponse|Application|Response
    {
        if ($request->wantsJson() || ! View::exists($view)) {
            return response()->json($data);
        }

        return view($view, $data);
    }
}