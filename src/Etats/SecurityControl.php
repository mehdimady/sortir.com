<?php

namespace App\Etats;

use App\Entity\Participant;
use Symfony\Component\Security\Core\User\UserInterface;

class SecurityControl
{
    public function userIsActive ($user_current):bool{
        return $user_current->isActif();
    }
}