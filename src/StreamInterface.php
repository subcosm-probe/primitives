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


interface StreamInterface
{
    /**
     * returns the size of the stream if known otherwise null.
     *
     * @return int|null
     */
    public function getSize(): ? int;

    /**
     * returns the current position of the read/write pointer.
     *
     * @return int
     */
    public function getPosition(): int;

    /**
     * checks whether the stream is at the end or not.
     *
     * @return bool
     */
    public function isEndOfStream(): bool;

    /**
     * checks whether the stream is seekable or not.
     *
     * @return bool
     */
    public function isSeekable(): bool;

    /**
     * checks whether the stream is writable or not.
     *
     * @return bool
     */
    public function isWritable(): bool;

    /**
     * checks whether the stream is readable or not.
     *
     * @return bool
     */
    public function isReadable(): bool;

    /**
     * seeks to the provided offset of the stream.
     *
     * @param int $offset
     * @return StreamInterface
     */
    public function seek(int $offset): StreamInterface;

    /**
     * skips the provided offset and moves the pointer the provided number of bytes forward.
     *
     * @param int $bytes
     * @return StreamInterface
     */
    public function skip(int $bytes): StreamInterface;

    /**
     * moves the pointer to the end of the stream plus offset.
     *
     * @param int $offset
     * @return StreamInterface
     */
    public function ahead(int $offset = 0): StreamInterface;

    /**
     * rewinds the stream.
     *
     * @return StreamInterface
     */
    public function rewind(): StreamInterface;

    /**
     * writes the provided string to the current stream position.
     *
     * @param string $string
     * @return StreamInterface
     */
    public function write(string $string): StreamInterface;

    /**
     * reads the provides string, optionally limits the read bytes to the provided length. If no length is provided
     * the string contents from the current stream position until the end of the stream will be returned.
     *
     * @param int|null $length
     * @return string
     */
    public function read(int $length = null): string;

    /**
     * returns a stream_get_meta_data() compatible array of metadata.
     *
     * @link http://php.net/manual/en/function.stream-get-meta-data.php
     *
     * @param string|null $key
     * @return array|mixed|null
     */
    public function getMetadata(string $key = null);
}