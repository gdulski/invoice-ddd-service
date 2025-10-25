<?php

declare(strict_types=1);

namespace Src\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class InvoiceModel extends Model
{
    protected $table = 'invoices';
    
    protected $keyType = 'string';
    
    public $incrementing = false;
    
    protected $fillable = [
        'id',
        'status',
        'customer_name',
        'customer_email',
        'created_at',
        'updated_at'
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function lines(): HasMany
    {
        return $this->hasMany(InvoiceLineModel::class, 'invoice_id', 'id');
    }
}


