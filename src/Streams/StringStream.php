<?php
/**
 * This file is part of the subcosm-probe.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace Singularity\Primitive\Streams;


use Singularity\Primitive\Exceptions\StreamException;
use Singularity\Primitive\StreamInterface;

class StringStream implements StreamInterface
{
    /**
     * contains the contents of the stream.
     *
     * @var string
     */
    protected $contents;

    /**
     * contains an array of metadata of the stream.
     *
     * @var array
     */
    protected $metadata = [];

    /**
     * contains the current position of the stream.
     *
     * @var int
     */
    protected $position = 0;

    /**
     * StringStream constructor.
     * @param string $contents
     */
    public function __construct(string $contents)
    {
        $this->contents = $contents;

        $this->metadata = [
            'stream_type' => 'string',
            'mode' => 'r',
        ];
    }

    /**
     * returns the size of the stream if known otherwise null.
     *
     * @return int|null
     */
    public function getSize(): ? int
    {
        return strlen($this->contents);
    }

    /**
     * returns the current position of the read/write pointer.
     *
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * checks whether the stream is at the end or not.
     *
     * @return bool
     */
    public function isEndOfStream(): bool
    {
        return $this->position + 1 === $this->getSize();
    }

    /**
     * checks whether the stream is seekable or not.
     *
     * @return bool
     */
    public function isSeekable(): bool
    {
        return true;
    }

    /**
     * checks whether the stream is writable or not.
     *
     * @return bool
     */
    public function isWritable(): bool
    {
        return false;
    }

    /**
     * checks whether the stream is readable or not.
     *
     * @return bool
     */
    public function isReadable(): bool
    {
        return true;
    }

    /**
     * seeks to the provided offset of the stream.
     *
     * @param int $offset
     * @throws StreamException when the offset is out of bounds.
     * @return StreamInterface
     */
    public function seek(int $offset): StreamInterface
    {
        if ( $offset < 0 || $offset + 1 > $this->getSize() ) {
            throw new StreamException(
                'offset is out of bounds'
            );
        }

        $this->position = $offset;

        return $this;
    }

    /**
     * skips the provided offset and moves the pointer the provided number of bytes forward.
     *
     * @param int $bytes
     * @throws StreamException when the provided byte value is small than 0.
     * @throws StreamException when the provided byte value will exceed the size of the stream.
     * @return StreamInterface
     */
    public function skip(int $bytes): StreamInterface
    {
        if ( $bytes < 0 ) {
            throw new StreamException(
                'Bytes to skip can not be negative'
            );
        }

        if ( ( $this->position + $bytes ) + 1 > $this->getSize() ) {
            throw new StreamException(
                'offset is out of bounds'
            );
        }

        $this->position += $bytes;

        return $this;
    }

    /**
     * moves the pointer to the end of the stream plus offset.
     *
     * @param int $offset
     * @throws StreamException when offset is not 0.
     * @return StreamInterface
     */
    public function ahead(int $offset = 0): StreamInterface
    {
        $this->position = $this->getSize() - 1;

        if ( $offset !== 0 ) {
            throw new StreamException(
                'offset can not be used at string streams'
            );
        }

        return $this;
    }

    /**
     * rewinds the stream.
     *
     * @return StreamInterface
     */
    public function rewind(): StreamInterface
    {
        $this->position = 0;

        return $this;
    }

    /**
     * writes the provided string to the current stream position.
     *
     * @param string $string
     * @throws StreamException
     * @return StreamInterface
     */
    public function write(string $string): StreamInterface
    {
        throw new StreamException(
            'Stream streams are not writable'
        );
    }

    /**
     * reads the provides string, optionally limits the read bytes to the provided length. If no length is provided
     * the string contents from the current stream position until the end of the stream will be returned.
     *
     * @param int|null $length
     * @return string
     */
    public function read(int $length = null): string
    {
        if ( null === $length ) {
            return substr($this->contents, $this->position);
        }

        return substr($this->contents, $this->position, $length);
    }

    /**
     * returns a stream_get_meta_data() compatible array of metadata.
     *
     * @link http://php.net/manual/en/function.stream-get-meta-data.php
     *
     * @param string|null $key
     * @return array|mixed|null
     */
    public function getMetadata(string $key = null)
    {
        return $this->metadata;
    }

}