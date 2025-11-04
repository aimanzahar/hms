<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Appointment represents a scheduled consultation between a doctor and a patient.
 *
 * @property int $id
 * @property int $doctor_id
 * @property int $patient_id
 * @property \Illuminate\Support\Carbon $appointment_date
 * @property string $status
 * @property string|null $notes
 */
class Appointment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'doctor_id',
        'patient_id',
        'appointment_date',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'appointment_date' => 'datetime',
    ];

    /**
     * Get the doctor assigned to the appointment.
     *
     * @return BelongsTo<Doctor, Appointment>
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Get the patient for the appointment.
     *
     * @return BelongsTo<Patient, Appointment>
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the medical record associated with the appointment.
     *
     * @return HasOne<MedicalRecord>
     */
    public function medicalRecord(): HasOne
    {
        return $this->hasOne(MedicalRecord::class);
    }

    /**
     * Get the bill generated for the appointment.
     *
     * @return HasOne<Bill>
     */
    public function bill(): HasOne
    {
        return $this->hasOne(Bill::class);
    }
}