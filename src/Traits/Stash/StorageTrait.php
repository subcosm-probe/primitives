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
use Singularity\Primitive\Exceptions\StashException;


/**
 * Trait StorageTrait
 *
 * General Stash storage trait.
 *
 * @package Singularity\Primitive\Traits\Stash
 */
trait StorageTrait
{
    /**
     * @var array
     */
    protected $items = [];

    /**
     * stores the provided value to the provided key at the stash storage.
     *
     * @param $key
     * @param $value
     */
    protected function storeToStash($key, $value)
    {
        $this->items[$key] = $this->sanitizeStashStorageItem($value, $key);
    }

    /**
     * stores the provided items to the stash storage, replaces existing items.
     *
     * @param array $items
     */
    protected function storeToStashFromArray(array $items)
    {
        $items = $this->mapStashStorageItems($items, null);

        $this->items = array_replace($this->items, $items);
    }

    /**
     * returns the stash storage array.
     *
     * @return array
     */
    protected function fetchStashStorage(): array
    {
        return $this->items;
    }

    /**
     * returns the stash storage value for the provided key, if any, otherwise null.
     *
     * @param $key
     * @return null|mixed
     */
    protected function fetchStashStorageKey($key)
    {
        return $this->items[$key] ?? null;
    }

    /**
     * removes a stash storage value for the provided key, if any, otherwise null.
     *
     * @param $key
     */
    protected function removeStashStorageKey($key)
    {
        unset($this->items[$key]);
    }

    /**
     * checks whether a stash storage key was defined or not.
     *
     * @param $key
     * @return bool
     */
    protected function hasStashStorageValue($key): bool
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * stash storage item mapper.
     *
     * @param $item
     * @param $key
     * @return mixed
     * @throws StashException
     */
    private function mapStashStorageItems($item, $key)
    {
        if ( is_array($item) ) {
            foreach ( $item as $currentKey => $currentValue ) {
                $item[$currentKey] = $this->{__FUNCTION__}($currentValue, $currentKey);
            }

            return $item;
        }

        return $this->sanitizeStashStorageItem($item, $key);
    }

    /**
     * value sanitizer for stash items.
     *
     * @param $item
     * @param $key
     * @return mixed
     * @throws StashException
     */
    private function sanitizeStashStorageItem($item, $key)
    {
        if ( is_object($item) && ! ( $item instanceof \JsonSerializable ) ) {
            throw new StashException(
                'Can not stash value of key `'.$key.'`, provided object does not implement JsonSerializable'
            );
        }

        return $item;
    }
}