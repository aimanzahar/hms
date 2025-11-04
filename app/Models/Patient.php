<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * Class Patient represents a healthcare recipient within the system.
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $date_of_birth
 * @property string|null $gender
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $emergency_contact
 * @property string|null $medical_history
 */
class Patient extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'date_of_birth',
        'gender',
        'phone',
        'address',
        'emergency_contact',
        'medical_history',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_birth' => 'date',
    ];

    /**
     * Get the user that owns the patient profile.
     *
     * @return BelongsTo<User, Patient>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the appointments scheduled for the patient.
     *
     * @return HasMany<Appointment>
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get the bills associated with the patient.
     *
     * @return HasMany<Bill>
     */
    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    /**
     * Get the medical records associated with the patient through appointments.
     *
     * @return HasManyThrough<MedicalRecord>
     */
    public function medicalRecords(): HasManyThrough
    {
        return $this->hasManyThrough(
            MedicalRecord::class,
            Appointment::class,
            'patient_id',
            'appointment_id'
        );
    }
}