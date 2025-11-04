<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientProfileUpdateRequest;
use App\Models\Appointment;
use App\Models\Bill;
use App\Models\Patient;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class PatientController extends Controller
{
    /**
     * Display a listing of patients (doctor only).
     */
    public function index(Request $request): ViewContract|JsonResponse|Response
    {
        $user = $request->user();

        abort_unless($user && $user->isDoctor(), 403);

        $query = Patient::query()
            ->with([
                'user:id,name,email',
            ])
            ->withCount(['appointments', 'medicalRecords', 'bills'])
            ->orderBy('appointments_count', 'desc')
            ->orderBy('id');

        if ($request->filled('search')) {
            $term = $request->string('search')->trim();
            $query->whereHas('user', function ($q) use ($term) {
                $q->where('name', 'like', '%'.$term.'%')
                    ->orWhere('email', 'like', '%'.$term.'%');
            });
        }

        $patients = $query->paginate(15)->withQueryString();

        return $this->respond($request, 'patient.index', [
            'patients' => $patients,
        ]);
    }

    /**
     * Display the specified patient profile.
     */
    public function show(Request $request, Patient $patient): ViewContract|JsonResponse|Response
    {
        $user = $request->user();

        abort_unless(
            $user && ($user->isDoctor() || $user->id === $patient->user_id),
            403
        );

        $patient->load([
            'user:id,name,email',
            'appointments' => function ($query) {
                $query->latest('appointment_date')
                    ->with([
                        'doctor.user:id,name,email',
                    ])
                    ->take(10);
            },
            'medicalRecords.appointment.doctor.user:id,name,email',
            'bills.items',
        ]);

        return $this->respond($request, 'patient.show', [
            'patient' => $patient,
        ]);
    }

    /**
     * Show authenticated patient's own profile.
     */
    public function profile(Request $request): ViewContract|JsonResponse|Response
    {
        $user = $request->user();

        abort_unless($user && $user->isPatient(), 403);

        $patient = $user->patient()
            ->with([
                'appointments' => function ($query) {
                    $query->where('appointment_date', '>=', Carbon::now()->startOfDay())
                        ->orderBy('appointment_date')
                        ->with('doctor.user:id,name,email')
                        ->take(5);
                },
                'medicalRecords.appointment.doctor.user:id,name,email',
                'bills' => function ($query) {
                    $query->orderByDesc('created_at')
                        ->with('items')
                        ->take(5);
                },
            ])
            ->first();

        $statistics = [
            'upcoming_appointments' => Appointment::query()
                ->where('patient_id', optional($patient)->id)
                ->where('appointment_date', '>=', Carbon::now())
                ->count(),
            'completed_appointments' => Appointment::query()
                ->where('patient_id', optional($patient)->id)
                ->where('status', 'completed')
                ->count(),
            'unpaid_bills' => Bill::query()
                ->where('patient_id', optional($patient)->id)
                ->where('status', 'unpaid')
                ->sum('total_amount'),
        ];

        return $this->respond($request, 'patient.profile', [
            'patient' => $patient,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Update the authenticated patient's profile.
     */
    public function updateProfile(PatientProfileUpdateRequest $request): RedirectResponse|JsonResponse
    {
        $user = $request->user();
        $patient = $user->patient()->firstOrNew();

        if (! $patient->exists) {
            $patient->user()->associate($user);
        }

        $patient->fill($request->validated());
        $patient->save();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => __('Patient profile updated successfully.'),
                'patient' => $patient->load('user:id,name,email'),
            ]);
        }

        return redirect()
            ->route('patient.profile')
            ->with('status', __('Patient profile updated successfully.'));
    }
}