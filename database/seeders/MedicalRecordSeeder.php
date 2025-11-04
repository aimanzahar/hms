<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\MedicalRecord;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MedicalRecordSeeder extends Seeder
{
    public function run(): void
    {
        $recordTemplates = [
            [
                'diagnosis' => 'Upper Respiratory Tract Infection (URTI)',
                'treatment' => 'Advise rest, adequate hydration, salt-water gargle, and monitor temperature daily.',
                'prescription' => 'Paracetamol 500mg – 1 tablet every 6 hours for 3 days; Amoxicillin 250mg – 1 capsule three times daily for 5 days.',
                'notes' => 'Return if fever persists beyond 3 days or breathing difficulty occurs.',
            ],
            [
                'diagnosis' => 'Hypertension',
                'treatment' => 'Review lifestyle, reduce salt intake, encourage brisk walking 30 minutes daily, monitor blood pressure at home.',
                'prescription' => 'Amlodipine 5mg – 1 tablet once daily in the morning.',
                'notes' => 'Schedule follow-up in 4 weeks with blood pressure log.',
            ],
            [
                'diagnosis' => 'Type 2 Diabetes Mellitus',
                'treatment' => 'Dietary counselling emphasising low glycaemic index foods, encourage regular exercise, monitor fasting glucose.',
                'prescription' => 'Metformin 500mg – 1 tablet twice daily after meals.',
                'notes' => 'Arrange HbA1c test in 3 months and monitor for hypoglycaemia symptoms.',
            ],
            [
                'diagnosis' => 'Gastritis',
                'treatment' => 'Advise small frequent meals, avoid spicy food, caffeine, and late-night eating.',
                'prescription' => 'Omeprazole 20mg – 1 capsule before breakfast daily for 14 days; Antacid suspension 10ml after meals as needed.',
                'notes' => 'Seek care if vomiting blood or black stools occur.',
            ],
            [
                'diagnosis' => 'Dengue Fever (Recovered)',
                'treatment' => 'Continue oral rehydration, monitor for warning signs, avoid NSAIDs.',
                'prescription' => 'Paracetamol 500mg – 1 tablet every 6 hours for pain or fever, maximum 4g daily.',
                'notes' => 'Repeat full blood count in 48 hours or sooner if persistent abdominal pain or bleeding.',
            ],
            [
                'diagnosis' => 'Migraine',
                'treatment' => 'Identify triggers, ensure adequate hydration, maintain regular sleep pattern.',
                'prescription' => 'Sumatriptan 50mg – 1 tablet at onset; Paracetamol 500mg – 1 tablet every 6 hours as needed.',
                'notes' => 'Return if headaches increase in frequency or severity.',
            ],
            [
                'diagnosis' => 'Lower Back Pain',
                'treatment' => 'Recommend physiotherapy stretching exercises, avoid heavy lifting, apply warm compress.',
                'prescription' => 'Paracetamol 500mg – 1 tablet every 6 hours for pain; Diazepam 2mg – 1 tablet at night for 3 days if muscle spasm present.',
                'notes' => 'Follow up in 2 weeks; consider imaging if no improvement.',
            ],
        ];

        $completedAppointments = Appointment::query()
            ->where('status', 'completed')
            ->get();

        if ($completedAppointments->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($completedAppointments, $recordTemplates) {
            foreach ($completedAppointments as $appointment) {
                $template = $recordTemplates[array_rand($recordTemplates)];

                MedicalRecord::updateOrCreate(
                    ['appointment_id' => $appointment->id],
                    [
                        'diagnosis' => $template['diagnosis'],
                        'treatment' => $template['treatment'],
                        'prescription' => $template['prescription'],
                        'notes' => $template['notes'],
                    ]
                );
            }
        });
    }
}