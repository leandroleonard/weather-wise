<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LoginService
{
    public function attempt(string $email, string $password): ?User
    {
        try {
            $user = User::where('email', $email)->first();

            if (! $user) {
                return null;
            }

            if (! Hash::check($password, $user->pass)) {
                return null;
            }


            return $user;
        } catch (\Throwable $e) {
            Log::error('LoginService error: '.$e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }
}
