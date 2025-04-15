<?php

namespace App\Enum;

enum ContractType: string
{
    case CDI = 'CDI';
    case CDD = 'CDD';
    case INTERNSHIP = 'Internship';
    case FREELANCE = 'Freelance';
}
