<?php

namespace App\Enum;

enum ApplicationStatus: string
{
    case EN_ATTENTE = 'En attente';
    case ACCEPTEE = 'Acceptée';
    case REFUSEE = 'Refusée';
}
