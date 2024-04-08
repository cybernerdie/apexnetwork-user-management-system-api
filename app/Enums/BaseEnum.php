<?php

namespace App\Enums;

class BaseEnum
{
    /**
     * Get all defined constants for the enum.
     *
     * @return array<string, mixed>
     */
    public static function getValues(): array
    {
        return static::getConstants();
    }

    /**
     * Get all constants defined on the class.
     *
     * @return array<string, mixed>
     */
    protected static function getConstants(): array
    {
        $reflectionClass = new \ReflectionClass(static::class);
        return $reflectionClass->getConstants();
    }
}
