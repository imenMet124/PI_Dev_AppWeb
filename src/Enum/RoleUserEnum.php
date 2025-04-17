<?php

namespace App\Enum;

enum RoleUserEnum: string
{
    case ADMIN = 'admin';
    case USER = 'user';
    case MANAGER = 'manager';
} 