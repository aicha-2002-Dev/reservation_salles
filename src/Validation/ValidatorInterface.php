<?php

namespace App\Validation;
use App\Validation\ValidationResult;

interface ValidatorInterface
{
    public function validate(array $data): ValidationResult;
}