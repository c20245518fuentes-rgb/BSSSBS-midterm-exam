<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingInvoice extends Model
{
    use HasFactory;

    public const PAYMENT_UNPAID = 'Unpaid';

    public const PAYMENT_PAID = 'Paid';

    public const PAYMENT_REFUNDED = 'Refunded';

    protected $fillable = ['appointment_id', 'total_amount', 'payment_status', 'paid_at'];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}
