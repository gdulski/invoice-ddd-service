<?php

declare(strict_types=1);

namespace Src\Presentation\Controllers;

use Src\Application\DTOs\CreateInvoiceCommand;
use Src\Application\DTOs\InvoiceLineDto;
use Src\Application\DTOs\SendInvoiceCommand;
use Src\Application\DTOs\ViewInvoiceQuery;
use Src\Application\Handlers\CreateInvoiceHandler;
use Src\Application\Handlers\SendInvoiceHandler;
use Src\Application\Handlers\ViewInvoiceHandler;
use Src\Presentation\Requests\CreateInvoiceRequest;
use Src\Presentation\Requests\SendInvoiceRequest;
use Src\Presentation\Resources\InvoiceResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

final class InvoiceController
{
    public function __construct(
        private CreateInvoiceHandler $createInvoiceHandler,
        private ViewInvoiceHandler $viewInvoiceHandler,
        private SendInvoiceHandler $sendInvoiceHandler
    ) {}

    public function store(CreateInvoiceRequest $request): JsonResponse
    {
        $lines = [];
        foreach ($request->validated()['lines'] as $line) {
            $lines[] = new InvoiceLineDto(
                $line['product_name'],
                $line['quantity'],
                $line['unit_price_in_cents']
            );
        }

        $command = new CreateInvoiceCommand(
            $request->validated()['customer_name'],
            $request->validated()['customer_email'],
            $lines
        );

        $result = $this->createInvoiceHandler->handle($command);

        return response()->json(
            new InvoiceResource($result),
            Response::HTTP_CREATED
        );
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $query = new ViewInvoiceQuery($id);
        $result = $this->viewInvoiceHandler->handle($query);

        return response()->json(new InvoiceResource($result));
    }

    public function send(SendInvoiceRequest $request, string $id): JsonResponse
    {
        $command = new SendInvoiceCommand($id);
        $result = $this->sendInvoiceHandler->handle($command);

        return response()->json(new InvoiceResource($result));
    }
}


