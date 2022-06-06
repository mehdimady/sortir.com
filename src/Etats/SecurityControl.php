<?php

namespace App\Etats;

class SecurityControl
{
    public function userIsActive ($user_current):bool{
        return $user_current->isActif();
    }
}