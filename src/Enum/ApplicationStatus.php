<?php

namespace App\Enum;

enum ApplicationStatus: string
{
    case PENDING = 'pending';
    case SHORTLISTED = 'shortlisted';
    case INTERVIEW = 'interview';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
}
