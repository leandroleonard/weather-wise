<?php
namespace App\Services\Auth;

use App\Models\User;
use App\Models\UserSettings;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Throwable;

class RegisterService
{
    public function createUser(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'pass' => Hash::make($data['password']),
            ]);

            UserSettings::create(['user_id' => $user->id, 'property' => 'setup', 'value' => false]);

            return $user;
        });
    }
}
