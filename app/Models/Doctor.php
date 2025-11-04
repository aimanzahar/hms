<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * Class Doctor represents a healthcare provider within the system.
 *
 * @property int $id
 * @property int $user_id
 * @property string $specialization
 * @property string $license_number
 * @property int $experience_years
 * @property string $consultation_fee
 */
class Doctor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'specialization',
        'license_number',
        'experience_years',
        'consultation_fee',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'experience_years' => 'integer',
        'consultation_fee' => 'decimal:2',
    ];

    /**
     * Get the user that owns the doctor profile.
     *
     * @return BelongsTo<User, Doctor>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the appointments scheduled with the doctor.
     *
     * @return HasMany<Appointment>
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get the medical records associated with the doctor through appointments.
     *
     * @return HasManyThrough<MedicalRecord>
     */
    public function medicalRecords(): HasManyThrough
    {
        return $this->hasManyThrough(
            MedicalRecord::class,
            Appointment::class,
            'doctor_id',
            'appointment_id'
        );
    }
}