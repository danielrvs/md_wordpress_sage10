<?php

declare(strict_types=1);

namespace App\Domain\Billing\DTOs;

class BillingDTO
{
    public function __construct(
        public string $invoiceId,
        public int $doctorId,
        public float $amount,
        public string $currency,
        public string $status, // 'paid', 'open', 'uncollectible', 'void'
        public string $hostedInvoiceUrl,
        public string $createdAt
    ) {
    }

    public static function fromEloquent(object $model): self
    {
        return new self(
            invoiceId: $model->stripe_invoice_id,
            doctorId: (int) $model->doctor_id,
            amount: (float) ($model->amount / 100), // Stripe maneja céntimos
            currency: strtoupper($model->currency),
            status: $model->status,
            hostedInvoiceUrl: $model->invoice_pdf_url,
            createdAt: $model->created_at
        );
    }

    public function toArray(): array
    {
        return [
            'invoiceId' => $this->invoiceId,
            'doctorId' => $this->doctorId,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'status' => $this->status,
            'hostedInvoiceUrl' => $this->hostedInvoiceUrl,
            'createdAt' => $this->createdAt,
        ];
    }
}