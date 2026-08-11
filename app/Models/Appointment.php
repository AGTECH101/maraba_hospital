<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Service;

class Appointment extends Model
{
    protected $fillable = [
        'service_id',
        'service_ids',
        'staff_member_id',
        'user_id',
        'patient_name',
        'patient_email',
        'patient_phone',
        'appointment_date',
        'appointment_time',
        'notes',
        'status',
        'confirmation_code',
        'amount',
        'used_at',
    ];

    protected $casts = [
        'used_at' => 'datetime',
        'service_ids' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function staffMember(): BelongsTo
    {
        return $this->belongsTo(StaffMember::class);
    }

    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class);
    }

    public function selectedServices()
    {
        return Service::query()->whereIn('id', $this->service_ids ?? [])->get();
    }
}
