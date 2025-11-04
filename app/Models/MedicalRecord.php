<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class MedicalRecord stores clinical details captured during an appointment.
 *
 * @property int $id
 * @property int $appointment_id
 * @property string|null $diagnosis
 * @property string|null $treatment
 * @property string|null $prescription
 * @property string|null $notes
 */
class MedicalRecord extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'appointment_id',
        'diagnosis',
        'treatment',
        'prescription',
        'notes',
    ];

    /**
     * Get the appointment associated with the medical record.
     *
     * @return BelongsTo<Appointment, MedicalRecord>
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}