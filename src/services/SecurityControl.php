<?php

namespace App\services;

class SecurityControl
{
    public function userIsActive ($user_current):bool{
        return $user_current->isActif();
    }
}