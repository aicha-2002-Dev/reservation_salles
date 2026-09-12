<?php

declare(strict_types=1);

namespace App\Session;

final class PhpSessionManager implements SessionManagerInterface
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function flash(string $cle, string $message): void
    {
        $_SESSION[$cle] = $message;
    }

    public function recupererFlash(string $cle): ?string
    {
        $message = $_SESSION[$cle] ?? null;
        unset($_SESSION[$cle]);
        return $message;
    }
}