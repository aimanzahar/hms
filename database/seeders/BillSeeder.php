<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Bill;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BillSeeder extends Seeder
{
    public function run(): void
    {
        $completedAppointments = Appointment::query()
            ->where('status', 'completed')
            ->get();

        if ($completedAppointments->isEmpty()) {
            return;
        }

        $statusWeights = [
            'paid' => 0.60,
            'unpaid' => 0.30,
            'partial' => 0.10,
        ];

        $statusDistribution = $this->buildStatusDistribution($statusWeights, $completedAppointments->count());

        DB::transaction(function () use ($completedAppointments, $statusDistribution) {
            foreach ($completedAppointments->values() as $index => $appointment) {
                $status = $statusDistribution[$index] ?? 'unpaid';
                $totalAmount = $this->randomCurrency(100, 500);

                $appointmentDate = $appointment->appointment_date
                    ? Carbon::parse($appointment->appointment_date)
                    : Carbon::now();

                $dueDate = $appointmentDate->copy()->addDays(30)->toDateString();

                Bill::updateOrCreate(
                    [
                        'appointment_id' => $appointment->id,
                    ],
                    [
                        'patient_id' => $appointment->patient_id,
                        'total_amount' => $totalAmount,
                        'status' => $status,
                        'due_date' => $dueDate,
                    ]
                );
            }
        });
    }

    /**
     * Build an array of statuses honouring the provided weightings.
     *
     * @param  array<string, float>  $weights
     * @return list<string>
     */
    private function buildStatusDistribution(array $weights, int $count): array
    {
        $distribution = [];

        foreach ($weights as $status => $ratio) {
            $allocation = (int) round($ratio * $count);
            $distribution = array_merge($distribution, array_fill(0, $allocation, $status));
        }

        while (count($distribution) < $count) {
            $distribution[] = 'unpaid';
        }

        shuffle($distribution);

        return array_slice($distribution, 0, $count);
    }

    /**
     * Generate a random currency amount within the given bounds (inclusive).
     */
    private function randomCurrency(int $min, int $max): float
    {
        return random_int($min * 100, $max * 100) / 100;
    }
}