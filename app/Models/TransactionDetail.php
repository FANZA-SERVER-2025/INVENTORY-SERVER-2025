<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'item_id',
        'quantity',
        'unit_type',
        'sub_unit_type',
        'box_quantity',
        'sub_unit_quantity',
        'price',
        'subtotal',
        'discount',
        'bonus',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'box_quantity' => 'integer',
        'sub_unit_quantity' => 'integer',
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'bonus' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
