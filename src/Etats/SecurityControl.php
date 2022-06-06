<?php

namespace App\Etats;

use App\Entity\Participant;

class SecurityControl
{
public function UserIsActive(Participant $user_current){
    $user_current->isActif() ? true :false;
}
}