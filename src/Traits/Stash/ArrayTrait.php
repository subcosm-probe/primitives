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

/**
 * Trait ArrayTrait
 *
 * Array Access compliant implementation.
 *
 * @package Singularity\Primitive\Traits\Stash
 */
trait ArrayTrait
{
    use StorageTrait {
        fetchStashStorageKey as public offsetGet;
        storeToStash as public offsetSet;
        hasStashStorageValue as public offsetExists;
        removeStashStorageKey as public offsetUnset;
    }
}