<?php

namespace App\Enum;

enum UserStatus: string
{
    case ACTIVE = 'ACTIVE';
    case INACTIVE = 'INACTIVE';
    case ON_LEAVE = 'ON_LEAVE';
    case SUSPENDED = 'SUSPENDED';
    case TERMINATED = 'TERMINATED';
    case RESIGNED = 'RESIGNED';
    case RETIRED = 'RETIRED';
    case PROBATION = 'PROBATION';
    case CONTRACT_ENDED = 'CONTRACT_ENDED';

    public function getLabel(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::ON_LEAVE => 'On Leave',
            self::SUSPENDED => 'Suspended',
            self::TERMINATED => 'Terminated',
            self::RESIGNED => 'Resigned',
            self::RETIRED => 'Retired',
            self::PROBATION => 'Probation',
            self::CONTRACT_ENDED => 'Contract Ended',
        };
    }
} 