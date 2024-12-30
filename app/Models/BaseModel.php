<?php

namespace App\Models;

abstract class BaseModel
{
    /**
      * Construct an object from an array
      *
      * @param array<string,mixed> $array The array to construct the object from
      * @return object The object constructed from the array
     */
    abstract public static function constructFromArray(array $array): object;
}
