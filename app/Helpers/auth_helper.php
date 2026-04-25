<?php

use App\Models\UserModel;

if (! function_exists('current_user')) {
    function current_user(): ?array
    {
        $user = session('user');

        if (! is_array($user)) {
            return null;
        }

        return $user;
    }
}

if (! function_exists('current_user_id')) {
    function current_user_id(): ?int
    {
        return current_user()['id'] ?? null;
    }
}

if (! function_exists('is_logged_in')) {
    function is_logged_in(): bool
    {
        return current_user() !== null;
    }
}

if (! function_exists('is_admin')) {
    function is_admin(): bool
    {
        return (current_user()['role'] ?? null) === UserModel::ROLE_ADMIN;
    }
}

if (! function_exists('must_change_password')) {
    function must_change_password(): bool
    {
        return (bool) (current_user()['must_change_password'] ?? false);
    }
}

if (! function_exists('format_phone')) {
    function format_phone(?string $phone): string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone) ?? '';

        if (strlen($digits) === 10) {
            return sprintf('(%s) %s-%s', substr($digits, 0, 2), substr($digits, 2, 4), substr($digits, 6, 4));
        }

        if (strlen($digits) === 11) {
            return sprintf('(%s) %s-%s-%s', substr($digits, 0, 2), substr($digits, 2, 1), substr($digits, 3, 4), substr($digits, 7, 4));
        }

        if ($digits === '') {
            return $digits;
        }

        return $digits;
    }
}
