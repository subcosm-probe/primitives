<?php
/**
 * This file is part of the subcosm-probe.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace Singularity\Primitive\Traits\Stash;


use Singularity\Primitive\AbstractStash;

trait FactoryMethodTrait
{
    /**
     * Factory method.
     *
     * @param array $items
     * @return AbstractStash
     */
    public static function from(array $items): AbstractStash
    {
        return new static($items);
    }
}