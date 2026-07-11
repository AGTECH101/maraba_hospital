<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'appointment_id',
        'transaction_reference',
        'invoice_number',
        'payment_method',
        'status',
        'amount',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}
