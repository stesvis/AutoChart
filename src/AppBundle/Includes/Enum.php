<?php

namespace AppBundle\Includes;

abstract class Enum
{
    private static $constantsCache = [];
    private $value;

    public function __construct($value)
    {
        if (!self::has($value)) {
            throw new \UnexpectedValueException("Value '$value' is not part of the enum " . get_called_class());
        }

        $this->value = $value;
    }

    public static function has($value)
    {
        return in_array($value, self::toArray(), true);
    }

    public static function toArray()
    {
        $calledClass = get_called_class();

        if (!array_key_exists($calledClass, self::$constantsCache)) {
            $reflection = new \ReflectionClass($calledClass);
            self::$constantsCache[$calledClass] = $reflection->getConstants();
        }

        return self::$constantsCache[$calledClass];
    }

    public function is($value)
    {
        return $this->value === $value;
    }

    public function value()
    {
        return $this->value;
    }
}
