<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $statusPool = [
            'completed' => 0.40,
            'confirmed' => 0.30,
            'pending' => 0.20,
            'cancelled' => 0.10,
        ];

        $notesPool = [
            'completed' => [
                'Health screening follow-up',
                'Diabetes management review',
                'Post-surgery assessment',
                'Blood pressure monitoring visit',
                'Physiotherapy progress check',
                'Follow-up after dengue treatment',
                'Medication review session',
                'Child immunisation review',
            ],
            'confirmed' => [
                'Routine health screening',
                'Prenatal checkup',
                'Dermatology assessment',
                'Cardiology consultation',
                'Orthopedic evaluation',
                'Eye examination',
                'Respiratory symptom review',
            ],
            'pending' => [
                'Initial consultation requested',
                'Requested for allergy assessment',
                'Awaiting confirmation for gastric issues',
                'Patient requested ENT review',
                'Follow-up booking for back pain',
            ],
            'cancelled' => [
                'Cancelled due to patient travel',
                'Rescheduled for personal reasons',
                'Cancelled due to fever',
                'Patient opted for teleconsultation',
            ],
        ];

        $appointmentCount = 42;
        $weekdays = [1, 2, 3, 4, 5]; // Monday to Friday

        $doctors = Doctor::pluck('id')->all();
        $patients = Patient::pluck('id')->all();

        if (empty($doctors) || empty($patients)) {
            return;
        }

        DB::transaction(function () use (
            $appointmentCount,
            $statusPool,
            $notesPool,
            $weekdays,
            $doctors,
            $patients
        ) {
            $statusDistribution = $this->buildStatusDistribution($statusPool, $appointmentCount);
            $appointments = [];

            for ($i = 0; $i < $appointmentCount; $i++) {
                $status = $statusDistribution[$i];
                $date = $this->randomDateWithinThreeMonths($weekdays);
                $time = $this->randomTimeSlot();
                $appointmentDate = Carbon::parse(
                    $date->format('Y-m-d') . ' ' . $time
                );

                $appointments[] = [
                    'doctor_id' => $doctors[array_rand($doctors)],
                    'patient_id' => $patients[array_rand($patients)],
                    'appointment_date' => $appointmentDate,
                    'status' => $status,
                    'notes' => $this->pickNote($notesPool[$status]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            Appointment::insert($appointments);
        });
    }

    /**
     * Build status distribution array honouring the provided weightings.
     *
     * @param  array<string,float>  $statusPool
     * @param  int  $count
     * @return list<string>
     */
    private function buildStatusDistribution(array $statusPool, int $count): array
    {
        $distribution = [];
        foreach ($statusPool as $status => $ratio) {
            $distribution = array_merge(
                $distribution,
                array_fill(0, (int) round($ratio * $count), $status)
            );
        }

        while (count($distribution) < $count) {
            $distribution[] = 'pending';
        }

        shuffle($distribution);

        return array_slice($distribution, 0, $count);
    }

    /**
     * Generate a random datetime within ±3 months focused on weekdays.
     *
     * @param  list<int>  $weekdays
     */
    private function randomDateWithinThreeMonths(array $weekdays): Carbon
    {
        $start = Carbon::now()->subMonths(2);
        $end = Carbon::now()->addMonths(1);
        $date = $start->copy()->addDays(random_int(0, $start->diffInDays($end)));

        while (!in_array($date->dayOfWeekIso, $weekdays, true)) {
            $date->addDay();
        }

        return $date;
    }

    /**
     * Generate a realistic clinic time slot between 9am and 5pm.
     */
    private function randomTimeSlot(): string
    {
        $hour = random_int(9, 16);
        $minute = random_int(0, 1) ? '00' : '30';

        return sprintf('%02d:%s:00', $hour, $minute);
    }

    /**
     * Pick a context-specific note.
     *
     * @param  list<string>  $notes
     */
    private function pickNote(array $notes): string
    {
        return $notes[array_rand($notes)];
    }
}