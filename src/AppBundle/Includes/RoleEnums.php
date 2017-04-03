<?php

namespace AppBundle\Includes;

class RoleEnums extends Enum
{
    const __default = self::User;

    const SuperAdmin = 'ROLE_SUPER_ADMIN';
    const Admin = 'ROLE_ADMIN';
    const User = 'ROLE_USER';
}
