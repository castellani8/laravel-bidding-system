<?php

namespace App\Filament\Pages;

use Illuminate\Database\Eloquent\Model;

class Register extends \Filament\Pages\Auth\Register
{
    protected function handleRegistration(array $data): Model
    {
        $user = parent::handleRegistration($data);
        $user->assignRole('boughter');

        return $user;
    }
}
