<?php

declare(strict_types=1);

namespace App\Session;

interface SessionManagerInterface
{
    public function flash(string $cle, string $message): void;
    public function recupererFlash(string $cle): ?string;
}