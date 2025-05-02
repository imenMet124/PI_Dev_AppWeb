<?php

namespace App\Enum;

enum UserRole: string
{
    case RESPONSABLE_RH = 'RESPONSABLE_RH';
    case CHEF_PROJET = 'CHEF_PROJET';
    case EMPLOYE = 'EMPLOYE';
    case GUEST = 'GUEST';

    public function getLabel(): string
    {
        return match($this) {
            self::RESPONSABLE_RH => 'HR Manager',
            self::CHEF_PROJET => 'Project Manager',
            self::EMPLOYE => 'Employee',
            self::GUEST => 'Guest',
        };
    }
}