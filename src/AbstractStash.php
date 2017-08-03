<?php
/**
 * This file is part of the subcosm-probe.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace Singularity\Primitive;


abstract class AbstractStash
{
    /**
     * Stash constructor.
     * @param array $items
     */
    public function __construct(array $items)
    {
        $this->storeToStashFromArray($items);
    }


    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->fetchStashStorage();
    }

    abstract function fetchStashStorage(): array;

    abstract function storeToStashFromArray(array $items);
}