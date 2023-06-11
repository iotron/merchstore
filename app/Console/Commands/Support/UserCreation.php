<?php

namespace App\Console\Commands\Support;

use App\Models\Role;
use Filament\Support\Commands\Concerns\CanValidateInput;
use Filament\Facades\Filament;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Support\Facades\Hash;

trait UserCreation
{
    use CanValidateInput;

    public function createSuperAdmin()
    {
        $auth = Filament::auth();

        /** @var EloquentUserProvider $userProvider */
        $userProvider = $auth->getProvider();

        $userModel = $userProvider->getModel();

        $user = $userModel::create([
            'name' => 'FilamentSuperAdmin',
            'email' => 'admin@example.com',

            'password' => Hash::make('password'),
        ]);
        $user->assignRole(Role::Admin);
        $loginUrl = route('filament.auth.login');

        $this->info('Success! Super Admin Created :-');
        $this->info("Name : {$user->name}");
        $this->info("Email : {$user->email}");
        $this->info('Password : password');
        $this->info("Login here : {$loginUrl}");
    }
}
