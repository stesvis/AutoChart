<?php

namespace AppBundle\Includes;


class StaticFunctions
{
    /**
     * Serializes an object into an array
     *
     * @param $object
     * @return array
     */
    public static function serializeObject($object)
    {
        $reflectionClass = new \ReflectionClass(get_class($object));
        $array = array();

        foreach ($reflectionClass->getProperties() as $property) {
            $property->setAccessible(true);
//            if (!is_scalar($property->getValue($object))) {
//                $array[$property->getName()] = self::serializeObject($property->getValue($object));
//            } else {
            $array[$property->getName()] = $property->getValue($object);
//            }

            $property->setAccessible(false);
        }

        return $array;
    }

    /**
     * Checks if a variable is a valid JSON object
     *
     * @param array ...$args
     * @return bool
     */
    public static function isJson(...$args)
    {
        json_decode(...$args);
        return (json_last_error() === JSON_ERROR_NONE);
    }
}
