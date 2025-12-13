<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'supplier_id',
        'name',
        'code',
        'description',
        'unit',
        'box_type',
        'box_quantity',
        'stock',
        'minimum_stock',
        'purchase_price',
        'selling_price',
        'image',
        'is_active',
    ];

    protected $casts = [
        'stock' => 'integer',
        'minimum_stock' => 'integer',
        'box_quantity' => 'integer',
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    /**
     * Scope untuk low stock items
     */
    public function scopeLowStock($query)
    {
        return $query->whereRaw('stock < minimum_stock');
    }

    /**
     * Check if item is low on stock
     */
    public function isLowStock()
    {
        return $this->stock < $this->minimum_stock;
    }
}
