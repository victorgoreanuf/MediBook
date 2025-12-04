<?php

namespace App\Models\Appointment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User\User;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $doctor_id
 * @property int $patient_id
 * @property Carbon $start_time // Casted to datetime
 * @property Carbon $end_time // Casted to datetime
 * @property string $status
 * @property float $price // Casted to decimal:2
 * @property bool $is_paid // Casted to boolean
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read User $doctor // Relationship accessor
 * @property-read User $patient // Relationship accessor
 */

class Appointment extends Model
{
    protected $fillable = [
        'doctor_id',
        'patient_id',
        'start_time',
        'end_time',
        'status',
        'price',
        'is_paid'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_paid' => 'boolean',
        'price' => 'decimal:2'
    ];

    /**
     * The Doctor associated with this appointment.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * The Patient associated with this appointment.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }
}
