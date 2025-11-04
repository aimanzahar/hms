<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Bill represents a financial record associated with a patient visit.
 *
 * @property int $id
 * @property int $patient_id
 * @property int|null $appointment_id
 * @property string $total_amount
 * @property string $status
 * @property string|null $due_date
 */
class Bill extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'patient_id',
        'appointment_id',
        'total_amount',
        'status',
        'due_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_amount' => 'decimal:2',
        'due_date' => 'date',
    ];

    /**
     * Get the patient that owns the bill.
     *
     * @return BelongsTo<Patient, Bill>
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the appointment associated with the bill.
     *
     * @return BelongsTo<Appointment, Bill>
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Get the bill items for the bill.
     *
     * @return HasMany<BillItem>
     */
    public function items(): HasMany
    {
        return $this->hasMany(BillItem::class);
    }
}