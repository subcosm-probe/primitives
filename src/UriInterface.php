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


use Singularity\Primitive\Exceptions\UriException;

/**
 * Interface UriInterface
 *
 * The uri interface.
 *
 * @package Singularity\Primitive
 * @author Matthias Kaschubowski <nihylum@gmail.com>
 */
interface UriInterface
{
    /**
     * detects whether this is a local uri or not.
     *
     * @return bool
     */
    public function isLocal(): bool;

    /**
     * detects whether this is a remote uri or not.
     *
     * @return bool
     */
    public function isRemote(): bool;

    /**
     * returns the scheme, if any, otherwise null.
     *
     * @return null|string
     */
    public function getScheme(): ? string;

    /**
     * creates a new instance of the current exact uri and changes the scheme to the provided string.
     *
     * @param string $scheme
     * @return UriInterface
     */
    public function withScheme(string $scheme): UriInterface;

    /**
     * returns the authority part (<user info>@<hostname>:<port>).
     *
     * @link https://tools.ietf.org/html/rfc3986#section-3.2
     *
     * @return null|string
     */
    public function getAuthority(): ? string;

    /**
     * returns the user info of the uri. An optional password is delimited by an colon (:).
     *
     * @return null|string
     */
    public function getUserInfo(): ? string;

    /**
     * creates a new instance of the current exact uri and changes the user info by
     * setting the provided username and optionally the provided password as user
     * info of the uri.
     *
     * @param string $user
     * @param string|null $password
     * @return UriInterface
     */
    public function withUserInfo(string $user, string $password = null): UriInterface;

    /**
     * creates a new instance of the current exact uri and changes the user info by
     * removing any user info data.
     *
     * @return UriInterface
     */
    public function withoutUserInfo(): UriInterface;

    /**
     * returns the hostname of the uri, if any, otherwise null.
     *
     * @return null|string
     */
    public function getHost(): ? string;

    /**
     * creates a new instance of the current exact uri and changes the host name to
     * the provided host string.
     *
     * @param string $host
     * @return UriInterface
     */
    public function withHost(string $host): UriInterface;

    /**
     * returns the port of the uri, if any, otherwise null.
     *
     * This method will return 80 when http is set as the scheme of the uri.
     *
     * @return int|null
     */
    public function getPort(): ? int;

    /**
     * checks whether a default port is not omitted when constructing the uri string.
     *
     * @return bool
     */
    public function doesImplementPort(): bool;

    /**
     * creates a new instance of the current exact uri and changes the port to
     * the provided port integer.
     *
     * @param int $port
     * @throws UriException when the port integer is not inside the legal port range.
     * @return UriInterface
     */
    public function withPort(int $port): UriInterface;

    /**
     * creates a new instance of the current exact uri and changes the port by
     * removing it from the uri.
     *
     * This method will let the uri fallback to the scheme's default port (if any).
     *
     * @return UriInterface
     */
    public function withoutPort(): UriInterface;

    /**
     * returns the path of the uri. When no path is given, this method will return: /
     *
     * @return string
     */
    public function getPath(): string;

    /**
     * creates a new instance of the current exact uri and changes the path to
     * the provided path string.
     *
     * When the provided path string does not start with a slash (/), an slash will be
     * preceded.
     *
     * @param string $path
     * @return UriInterface
     */
    public function withPath(string $path): UriInterface;

    /**
     * creates a new instance of the current exact uri and changes the path by
     * removing it.
     *
     * This method will let the uri fallback to the default path: /
     *
     * @return UriInterface
     */
    public function withoutPath(): UriInterface;

    /**
     * returns the query string of the uri.
     *
     * @return null|string
     */
    public function getQuery(): ? string;

    /**
     * returns the query of the uri as an associated array.
     *
     * @return array
     */
    public function getQueryAsArray(): array;

    /**
     * creates a new instance of the current exact uri and changes the query to
     * the provided query string.
     *
     * @param string $query
     * @return UriInterface
     */
    public function withQuery(string $query): UriInterface;

    /**
     * creates a new instance of the current exact uri and changes the query to
     * the provided query array string representation.
     *
     * The contents of the provided array will replace any previously stored query
     * data.
     *
     * All contents of the provided data will collapse to a string, if objects are
     * set, this method will try to cast them to string.
     *
     * @param array $data
     * @return UriInterface
     */
    public function withQueryFromArray(array $data): UriInterface;

    /**
     * creates a new instance of the current exact uri and changes the query by
     * removing any data.
     *
     * @return UriInterface
     */
    public function withoutQuery(): UriInterface;

    /**
     * returns the fragment data of the uri, if any, otherwise null.
     *
     * @return null|string
     */
    public function getFragment(): ? string;

    /**
     * creates a new instance of the current exact uri and changes the fragment to
     * the provided fragment string.
     *
     * This method will NOT remove preceding hash tags.
     *
     * @param string $fragment
     * @return UriInterface
     */
    public function withFragment(string $fragment): UriInterface;

    /**
     * creates a new instance of the current exact uri and changes the fragment by
     * removing any fragment data.
     *
     * @return UriInterface
     */
    public function withoutFragment(): UriInterface;

    /**
     * renders the uri based on the currently known data.
     *
     * This method will omit default ports from the uri when they are not explicitly set.
     *
     * @return string
     */
    public function getUri(): string;

    /**
     * Uri stringifier.
     *
     * calls usually getUri().
     *
     * @return string
     */
    public function __toString(): string;
}