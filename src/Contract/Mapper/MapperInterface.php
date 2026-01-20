<?php
declare(strict_types=1);

namespace ControlBit\Dto\Contract\Mapper;

interface MapperInterface
{
    /**
     * Maps source data to a destination object.
     *
     * @template T of object
     * @param  object|array<string,mixed>  $source       The source data - either an object or associative array
     * @param  class-string<T>|T|null      $destination  The destination - either a class name string, an existing
     *                                                   object instance, or null to auto-detect from source
     *
     * @return T The mapped destination object - instance of the destination class if string provided, or same type as
     *           destination object if object provided
     */
    public function map(object|array $source, string|object|null $destination = null): object;

     /**
      * @template T of object
      * @param  array<object>          $source
      * @param  class-string<T>|null  $destination
      *
      * @return array<T>
      */
    public function mapCollection(array $source, string $destination = null): array;
}