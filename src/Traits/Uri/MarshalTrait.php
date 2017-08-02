<?php
/**
 * This file is part of the subcosm-probe.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace Singularity\Primitive\Traits\Uri;


use Singularity\Primitive\Exceptions\UriException;

trait MarshalTrait
{
    static protected $unreservedCharacters = 'a-zA-Z0-9_\-\.~';
    static protected $subDelimitedCharacters = '!\$&\'\(\)\*\+,;=';

    /**
     * marshals the scheme.
     *
     * @param $scheme
     * @return null|string
     * @throws UriException
     */
    protected function marshalScheme($scheme): ? string
    {
        if ( null === $scheme ) {
            return null;
        }

        if ( ! is_string($scheme) ) {
            throw new UriException(
                'Scheme must be a string'
            );
        }

        return strtolower($scheme);
    }

    /**
     * marshals the host.
     *
     * @param $host
     * @return null|string
     * @throws UriException
     */
    protected function marshalHost($host): ? string
    {
        if ( ! is_string($host) ) {
            throw new UriException(
                'Host must be a string'
            );
        }

        return strtolower($host);
    }

    /**
     * marshals the port.
     *
     * @param $port
     * @return int|null
     * @throws UriException
     */
    protected function marshalPort($port): ? int
    {
        if ( null === $port ) {
            return null;
        }

        $port = (int) $port;

        if ( 0 > $port || 65535 < $port ) {
            throw new UriException(
                sprintf('Invalid port: %s, value must be between 0 and 65535', $port)
            );
        }

        return $port;
    }

    /**
     * marshals the path.
     *
     * @param $path
     * @return string
     * @throws UriException
     */
    protected function marshalPath($path): string
    {
        if ( null === $path ) {
            $path = '/';
        }

        if ( ! is_string($path) ) {
            throw new UriException(
                'Path must be a string or null'
            );
        }

        return preg_replace_callback(
            '/(?:[^'.self::$unreservedCharacters.self::$subDelimitedCharacters.'%:@\/]++|%(?![A-Fa-f0-9]{2}))/',
            function($match) {
                return rawurlencode($match[0]);
            },
            $path
        );
    }

    /**
     * marshals the query and fragment.
     *
     * @param $string
     * @return string
     * @throws UriException
     */
    protected function marshalQueryAndFragment($string): string
    {
        if ( ! is_string($string) ) {
            throw new UriException(
                'Query and Fragment must be a string or null'
            );
        }

        return preg_replace_callback(
            '/(?:[^'.self::$unreservedCharacters.self::$subDelimitedCharacters.'%:@\/]++|%(?![A-Fa-f0-9]{2}))/',
            function($match) {
                return rawurlencode($match[0]);
            },
            $string
        );
    }
}