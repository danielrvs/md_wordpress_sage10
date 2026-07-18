<?php

declare(strict_types=1);

namespace App\Domain\Subscriptions\DTOs;

class SubscriptionDTO
{
    public function __construct(
        public int $doctorId,
        public string $planName, // 'basic', 'premium', 'enterprise'
        public string $status,   // 'active', 'trialing', 'past_due', 'canceled'
        public string $nextBillingAt
    ) {
    }

    public static function fromMeta(int $doctorId): self
    {
        return new self(
            doctorId: $doctorId,
            planName: get_user_meta($doctorId, 'stripe_plan_name', true) ?: 'basic',
            status: get_user_meta($doctorId, 'stripe_sub_status', true) ?: 'inactive',
            nextBillingAt: get_user_meta($doctorId, 'stripe_next_billing', true) ?: ''
        );
    }

    public function toArray(): array
    {
        return [
            'doctorId' => $this->doctorId,
            'planName' => $this->planName,
            'status' => $this->status,
            'nextBillingAt' => $this->nextBillingAt,
        ];
    }
}