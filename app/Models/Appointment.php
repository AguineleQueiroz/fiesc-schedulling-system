<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    protected $fillable = [
        'attendant_id',
        'client_name',
        'client_phone',
        'date',
        'start_time',
        'end_time',
        'status',
    ];

    public function attendant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'attendant_id');
    }

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'status' => AppointmentStatus::class,
        ];
    }
}
