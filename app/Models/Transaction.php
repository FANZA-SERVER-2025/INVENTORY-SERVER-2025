<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'transaction_number',
        'user_id',
        'type',
        'transaction_date',
        'total_amount',
        'discount',
        'bonus',
        'notes',
        'vehicle_id',
        'store_name',
        'payment_status',
        'customer_name',
        'customer_address',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'total_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'bonus' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    /**
     * Scopes
     */
    public function scopeTypeIn($query)
    {
        return $query->where('type', 'in');
    }

    public function scopeTypeOut($query)
    {
        return $query->where('type', 'out');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('transaction_date', now()->month)
                    ->whereYear('transaction_date', now()->year);
    }
}
