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


use Singularity\Primitive\Exceptions\StreamException;

class Stream implements StreamInterface
{
    /**
     * holds the stream resource
     *
     * @var null|resource
     */
    private $stream = null;

    /**
     * holds the size, or null.
     *
     * @var int|null
     */
    private $size = null;

    /**
     * defines if the stream is seekable.
     *
     * @var bool
     */
    private $seekable = false;

    /**
     * defines if the stream is readable.
     *
     * @var bool
     */
    private $readable = false;

    /**
     * defines if the stream is seekable.
     *
     * @var bool
     */
    private $writable = false;

    /**
     * holds the uri of the string as long an uri is resolvable.
     *
     * @var string|null
     */
    private $uri;

    /**
     * holds the custom meta data of the stream.
     *
     * @var array|mixed
     */
    private $customMetadata = [];

    /**
     * defines all hashes for read access.
     */
    private const READ_HASHES = [
        'r',
        'r+',
        'w+',
        'x+',
        'c+',
        'rb',
        'w+b',
        'r+b',
        'x+b',
        'c+b',
        'rt',
        'w+t',
        'r+t',
        'x+t',
        'c+t',
        'a+',
    ];

    /**
     * defines all hashes for write access.
     */
    private const WRITE_HASHES = [
        'w',
        'w+',
        'rw',
        'r+',
        'x+',
        'c+',
        'wb',
        'w+b',
        'r+b',
        'x+b',
        'c+b',
        'w+t',
        'r+b',
        'x+b',
        'c+b',
        'w+t',
        'r+t',
        'x+t',
        'c+t',
        'a',
        'a+',
    ];

    /**
     * Stream constructor.
     * @param $stream
     * @param array $options
     * @throws StreamException
     */
    public function __construct($stream, array $options = [])
    {
        if ( ! is_resource($stream) ) {
            throw new StreamException(
                'Stream argument must be a resource, '.gettype($stream).' given'
            );
        }

        if ( array_key_exists('size', $options) ) {
            $this->size = (int) $options['size'];
        }

        $this->customMetadata = $options['metadata'] ?? [];
        $this->stream = $stream;

        $meta = stream_get_meta_data($stream);

        $this->seekable = (bool) $meta['seekable'];
        $this->readable = in_array(self::READ_HASHES, $meta['mode']);
        $this->writable = in_array(self::WRITE_HASHES, $meta['mode']);
        $this->uri = $this->getMetadata('uri');
    }

    /**
     * Stream Destructor.
     */
    public function __destruct()
    {
        if ( is_resource($this->stream) ) {
            fclose($this->stream);
        }
    }

    /**
     * Stringifier.
     *
     * @return string
     */
    public function __toString()
    {
        try {
            $this->seek(0);
            return (string) stream_get_contents($this->stream);
        }
        catch ( StreamException $exception ) {
            return '';
        }
    }

    /**
     * returns the size of the stream if known otherwise null.
     *
     * @return int|null
     */
    public function getSize(): ? int
    {
        if ( $this->size !== null ) {
            return $this->size;
        }

        if ( ! is_resource($this->stream) ) {
            return null;
        }

        if ( $this->uri ) {
            clearstatcache(true, $this->uri);
        }

        $stats = fstat($this->stream);

        if ( array_key_exists('size', $stats) ) {
            return $this->size = $stats['size'];
        }

        return null;
    }

    /**
     * returns the current position of the read/write pointer.
     *
     * @throws StreamException when the stream is no longer available.
     * @throws StreamException when the stream position can not be determined.
     * @return int
     */
    public function getPosition(): int
    {
        if ( ! is_resource($this->stream) ) {
            throw new StreamException('Stream is not longer available');
        }

        $result = ftell($this->stream);

        if ( $result === false ) {
            throw new StreamException('Unable to determine stream position');
        }

        return $result;
    }

    /**
     * checks whether the stream is at the end or not.
     *
     * @throws StreamException when the stream is not longer available.
     * @return bool
     */
    public function isEndOfStream(): bool
    {
        if ( ! is_resource($this->stream) ) {
            throw new StreamException('Stream is not longer available');
        }

        return feof($this->stream);
    }

    /**
     * checks whether the stream is seekable or not.
     *
     * @return bool
     */
    public function isSeekable(): bool
    {
        return $this->seekable;
    }

    /**
     * checks whether the stream is writable or not.
     *
     * @return bool
     */
    public function isWritable(): bool
    {
        return $this->writable;
    }

    /**
     * checks whether the stream is readable or not.
     *
     * @return bool
     */
    public function isReadable(): bool
    {
        return $this->readable;
    }

    /**
     * seeks to the provided offset of the stream.
     *
     * @param int $offset
     * @throws StreamException when the stream is not longer available.
     * @throws StreamException when the stream position can not be reached.
     * @return StreamInterface
     */
    public function seek(int $offset): StreamInterface
    {
        if ( ! is_resource($this->stream) ) {
            throw new StreamException('Stream is not longer available');
        }

        if ( ! $this->seekable ) {
            throw new StreamException('Stream is not seekable');
        }

        $position = fseek($this->stream, $offset, SEEK_SET);

        if ( $position === -1 ) {
            throw new StreamException('Unable to seek to stream position');
        }

        return $this;
    }

    /**
     * skips the provided offset and moves the pointer the provided number of bytes forward.
     *
     * @param int $bytes
     * @throws StreamException when the stream is not longer available.
     * @throws StreamException when the stream position can not be reached.
     * @return StreamInterface
     */
    public function skip(int $bytes): StreamInterface
    {
        if ( ! is_resource($this->stream) ) {
            throw new StreamException('Stream is not longer available');
        }

        if ( ! $this->seekable ) {
            throw new StreamException('Stream is not seekable');
        }

        $position = fseek($this->stream, $bytes, SEEK_CUR);

        if ( $position === -1 ) {
            throw new StreamException('Unable to seek to stream position');
        }

        return $this;
    }

    /**
     * moves the pointer to the end of the stream plus offset.
     *
     * @param int $offset
     * @throws StreamException when the stream is not longer available.
     * @throws StreamException when the stream position can not be reached.
     * @return StreamInterface
     */
    public function ahead(int $offset = 0): StreamInterface
    {
        if ( ! is_resource($this->stream) ) {
            throw new StreamException('Stream is not longer available');
        }

        if ( ! $this->seekable ) {
            throw new StreamException('Stream is not seekable');
        }

        $position = fseek($this->stream, $offset, SEEK_END);

        if ( $position === -1 ) {
            throw new StreamException('Unable to seek to stream position');
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
        $this->seek(0);

        return $this;
    }

    /**
     * writes the provided string to the current stream position.
     *
     * @param string $string
     * @throws StreamException when the stream is not longer available.
     * @throws StreamException when the stream is not writable.
     * @throws StreamException when writing to the stream failed.
     * @return StreamInterface
     */
    public function write(string $string): StreamInterface
    {
        if ( ! is_resource($this->stream) ) {
            throw new StreamException('Stream is not longer available');
        }

        if ( ! $this->writable ) {
            throw new StreamException('Stream is not writable');
        }

        $this->size = null;
        $result = fwrite($this->stream, $string);

        if ( $result === false ) {
            throw new StreamException('Unable to write to stream');
        }

        return $this;
    }

    /**
     * reads the provides string, optionally limits the read bytes to the provided length. If no length is provided
     * the string contents from the current stream position until the end of the stream will be returned.
     *
     * @param int|null $length
     * @throws StreamException when the stream is not longer available.
     * @throws StreamException when the stream is not readable.
     * @throws StreamException when the length parameter is negative.
     * @throws StreamException when reading the stream has failed.
     * @return string
     */
    public function read(int $length = null): string
    {
        if ( ! is_resource($this->stream) ) {
            throw new StreamException('Stream is not longer available');
        }

        if ( ! $this->readable ) {
            throw new StreamException('Stream is not readable');
        }

        $length = $length ?? 0;

        if ( $length < 0 ) {
            throw new StreamException('Length parameter cannot be negative');
        }

        if ( 0 === $length ) {
            return '';
        }

        $string = fread($this->stream, $length);

        if ( false === $string ) {
            throw new StreamException('Unable to read from stream');
        }

        return $string;
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
        if ( ! is_resource($this->stream) ) {
            return is_null($key) ? [] : null;
        }

        if ( is_null($key) ) {
            return $this->customMetadata + stream_get_meta_data($this->stream);
        }

        if ( ! empty($this->customMetadata) ) {
            return $this->customMetadata;
        }

        $meta = stream_get_meta_data($this->stream);

        return ! is_null($key) && array_key_exists($key, $meta) ? $meta[$key] : null;
    }

}