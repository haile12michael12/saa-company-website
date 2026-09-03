<?php

namespace App\Tenant;

class TenantContext
{
    private mixed $tenant = null;

    public function set(mixed $tenant): void
    {
        $this->tenant = $tenant;
    }

    public function get(): mixed
    {
        return $this->tenant;
    }

    public function clear(): void
    {
        $this->tenant = null;
    }

    public function has(): bool
    {
        return $this->tenant !== null;
    }
}