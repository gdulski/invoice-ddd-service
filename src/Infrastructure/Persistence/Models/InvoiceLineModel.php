<?php

declare(strict_types=1);

namespace Src\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class InvoiceLineModel extends Model
{
    protected $table = 'invoice_lines';
    
    protected $fillable = [
        'invoice_id',
        'product_name',
        'quantity',
        'unit_price_in_cents',
        'created_at',
        'updated_at'
    ];
    
    protected $casts = [
        'quantity' => 'integer',
        'unit_price_in_cents' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(InvoiceModel::class, 'invoice_id', 'id');
    }
}


