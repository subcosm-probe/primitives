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


use Singularity\Primitive\Traits\Stash\ObjectTrait;

/**
 * Class Stash
 *
 * General json serializable object implementation.
 *
 * @package Singularity\Primitive
 */
class Stash extends AbstractStash implements \JsonSerializable
{
    use ObjectTrait;

    /**
     * Factory method.
     *
     * @param array $items
     * @return Stash
     */
    public static function from(array $items): Stash
    {
        return new static($items);
    }
}