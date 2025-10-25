<?php

declare(strict_types=1);

namespace Src\Domain\Repositories;

use Src\Domain\Entities\Invoice;
use Src\Domain\ValueObjects\InvoiceId;

interface InvoiceRepositoryInterface
{
    public function save(Invoice $invoice): void;
    
    public function findById(InvoiceId $id): ?Invoice;
    
    public function delete(InvoiceId $id): void;
}


