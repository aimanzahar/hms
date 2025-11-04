<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class BillItem represents an individual line item within a bill.
 *
 * @property int $id
 * @property int $bill_id
 * @property string $description
 * @property string $amount
 */
class BillItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'bill_id',
        'description',
        'amount',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Get the bill that owns the bill item.
     *
     * @return BelongsTo<Bill, BillItem>
     */
    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }
}