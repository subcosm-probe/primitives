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
 * Trait ObjectTrait
 *
 * __get, __set, __unset, __isset compliant implementation.
 *
 * @package Singularity\Primitive\Traits\Stash
 */
trait ObjectTrait
{
    use StorageTrait {
        fetchStashStorageKey as public __get;
        storeToStash as public __set;
        hasStashStorageValue as public __isset;
        removeStashStorageKey as public __unset;
    }
}