<?php

namespace AppBundle\Includes;

class StatusEnums extends Enum
{
    const __default = self::Active;

    const Active = 'A';
    const Deleted = 'D';
}
