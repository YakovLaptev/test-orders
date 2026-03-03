<?php

declare(strict_types = 1);

namespace App\Jobs\OrderExport;


use App\Models\Customer;

class CustomerDto
{
    public function __construct(
        public string $name,
        public string $email,
        public string $phone,
    )
    {
    }

    public static function makeFromCustomer(Customer $customer): self
    {
        return new self(
            name: $customer->name,
            email: $customer->email,
            phone: $customer->phone,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ];
    }
}
