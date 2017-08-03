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


use Singularity\Primitive\Traits\Stash\ArrayTrait;
use Singularity\Primitive\Traits\Stash\FactoryMethodTrait;

/**
 * Class ArrayStash
 *
 * General json serializable array access implementation.
 *
 * @package Singularity\Primitive
 */
class ArrayStash extends AbstractStash implements \ArrayAccess, \JsonSerializable, \Countable
{
    use ArrayTrait;
    use FactoryMethodTrait;
}