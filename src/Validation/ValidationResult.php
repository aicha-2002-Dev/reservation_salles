<?php

declare(strict_types=1);

namespace App\Validation;

final class ValidationResult
{
    
    private function __construct(
        private readonly bool $valid,
        private readonly array $errors,
        private readonly array $validatedData,
    ) {}

    public static function success(array $validatedData):self
    {
        return  new self(true,[],$validatedData);
    }

    public static function failure(array $errors):self
    {
        return  new self(false,$errors,[]);
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    public function errors(): array
    {
        return $this->errors;
    }
    public function validatedData(): array
    {
        return $this->validatedData;
    }
}

