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
use Singularity\Primitive\Traits\Uri\MarshalTrait;

class Uri implements UriInterface
{
    use MarshalTrait;

    protected const DEFAULT_PORTS = [
        'http' => 80,
        'https' => 443,
        'ftp' => 21,
        'gopher' => 70,
        'nntp' => 119,
        'news' => 119,
        'telnet' => 23,
        'tn3270' => 23,
        'imap' => 143,
        'pop' => 110,
        'ldap' => 389,
        'ssh' => 22,
    ];

    private const REPLACE_CHARACTERS = [
        '=' => '%3D',
        '&' => '%26',
    ];

    /**
     * @var null|string
     */
    private $scheme = null;

    /**
     * @var null|string
     */
    private $userInfo = null;

    /**
     * @var string|null
     */
    private $host = 'localhost';

    /**
     * @var null|int
     */
    private $port = null;

    /**
     * @var string
     */
    private $path = '/';

    /**
     * @var null|string
     */
    private $query = null;

    /**
     * @var null|string
     */
    private $fragment = null;

    public function __construct(string $uri = null)
    {
        if ( ! empty($uri) ) {
            $parts = parse_url($uri);

            if ( $parts === false ) {
                throw new UriException('Corrupt Uri');
            }

            $this->marshalInstance($parts);
        }
    }

    /**
     * detects whether this is a local uri or not.
     *
     * @return bool
     */
    public function isLocal(): bool
    {
        return $this->scheme === 'file';
    }

    /**
     * detects whether this is a remote uri or not.
     *
     * @return bool
     */
    public function isRemote(): bool
    {
        return $this->scheme !== 'file' && $this->host !== 'localhost';
    }

    /**
     * returns the scheme, if any, otherwise null.
     *
     * @return null|string
     */
    public function getScheme(): ? string
    {
        return $this->scheme;
    }

    /**
     * creates a new instance of the current exact uri and changes the scheme to the provided string.
     *
     * @param string $scheme
     * @return UriInterface
     */
    public function withScheme(string $scheme): UriInterface
    {
        $scheme = $this->marshalScheme($scheme);

        if ( $this->scheme === $scheme ) {
            return $this;
        }

        $instance = clone $this;
        $instance->scheme = $scheme;
        $instance->calibratePort();

        return $instance;
    }

    /**
     * returns the authority part (<user info>@<hostname>:<port>).
     *
     * @link https://tools.ietf.org/html/rfc3986#section-3.2
     *
     * @return null|string
     */
    public function getAuthority(): ? string
    {
        $authority = $this->host;

        if ( ! empty($this->userInfo) ) {
            $authority = $this->userInfo.'@'.$authority;
        }

        if ( null !== $this->port ) {
            $authority .= ':'.$this->port;
        }

        return $authority;
    }

    /**
     * returns the user info of the uri. An optional password is delimited by an colon (:).
     *
     * @return null|string
     */
    public function getUserInfo(): ? string
    {
        return $this->userInfo;
    }

    /**
     * creates a new instance of the current exact uri and changes the user info by
     * setting the provided username and optionally the provided password as user
     * info of the uri.
     *
     * @param string $user
     * @param string|null $password
     * @return UriInterface
     */
    public function withUserInfo(string $user, string $password = null): UriInterface
    {
        $info = $user;

        if ( null === $password ) {
            $info .= ':'.$password;
        }

        if ( $this->userInfo === $info ) {
            return $this;
        }

        $instance = clone $this;
        $instance->userInfo = $info;

        return $instance;
    }

    /**
     * creates a new instance of the current exact uri and changes the user info by
     * removing any user info data.
     *
     * @return UriInterface
     */
    public function withoutUserInfo(): UriInterface
    {
        $instance = clone $this;
        $instance->userInfo = null;

        return $instance;
    }

    /**
     * returns the hostname of the uri, if any, otherwise null.
     *
     * @return null|string
     */
    public function getHost(): ? string
    {
        return $this->host;
    }

    /**
     * creates a new instance of the current exact uri and changes the host name to
     * the provided host string.
     *
     * @param string $host
     * @return UriInterface
     */
    public function withHost(string $host): UriInterface
    {
        $instance = clone $this;
        $instance->host = $this->marshalHost($host);

        return $instance;
    }

    /**
     * returns the port of the uri, if any, otherwise null.
     *
     * This method will return 80 when http is set as the scheme of the uri.
     *
     * @return int|null
     */
    public function getPort(): ? int
    {
        if ( $this->port === null ) {
            return self::DEFAULT_PORTS[$this->scheme ?? ''] ?? null;
        }

        return $this->port;
    }

    /**
     * checks whether a default port is not omitted when constructing the uri string.
     *
     * @return bool
     */
    public function doesImplementPort(): bool
    {
        if ( $this->port === null ) {
            return array_key_exists($this->scheme, self::DEFAULT_PORTS);
        }

        return true;
    }

    /**
     * creates a new instance of the current exact uri and changes the port to
     * the provided port integer.
     *
     * @param int $port
     * @throws UriException when the port integer is not inside the legal port range.
     * @return UriInterface
     */
    public function withPort(int $port): UriInterface
    {
        $instance = clone $this;
        $instance->port = $this->marshalPort($port);

        return $instance;
    }

    /**
     * creates a new instance of the current exact uri and changes the port by
     * removing it from the uri.
     *
     * This method will let the uri fallback to the scheme's default port (if any).
     *
     * @return UriInterface
     */
    public function withoutPort(): UriInterface
    {
        $instance = clone $this;
        $instance->port = null;

        return $this;
    }

    /**
     * returns the path of the uri. When no path is given, this method will return: /
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

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
    public function withPath(string $path): UriInterface
    {
        $instance = clone $this;
        $instance->path = $this->marshalPath($path);

        return $instance;
    }

    /**
     * creates a new instance of the current exact uri and changes the path by
     * removing it.
     *
     * This method will let the uri fallback to the default path: /
     *
     * @return UriInterface
     */
    public function withoutPath(): UriInterface
    {
        $instance = clone $this;
        $instance->path = '/';

        return $instance;
    }

    /**
     * returns the query string of the uri.
     *
     * @return null|string
     */
    public function getQuery(): ? string
    {
        return $this->query;
    }

    /**
     * returns the query of the uri as an associated array.
     *
     * @return array
     */
    public function getQueryAsArray(): array
    {
        if ( $this->query === null ) {
            return [];
        }

        parse_str($this->query, $data);

        return $data;
    }

    /**
     * creates a new instance of the current exact uri and changes the query to
     * the provided query string.
     *
     * @param string $query
     * @return UriInterface
     */
    public function withQuery(string $query): UriInterface
    {
        $query = $this->marshalQueryAndFragment($query);

        if ( $this->query === $query ) {
            return $this;
        }

        $instance = clone $this;
        $instance->query = $query;

        return $instance;
    }

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
    public function withQueryFromArray(array $data): UriInterface
    {
        $query = http_build_query($data);

        if ( $this->query === $query ) {
            return $this;
        }

        $instance = clone $this;
        $instance->query = $query;

        return $instance;
    }

    /**
     * creates a new instance of the current exact uri and changes the query by
     * removing any data.
     *
     * @return UriInterface
     */
    public function withoutQuery(): UriInterface
    {
        $instance = clone $this;
        $instance->query = null;

        return $instance;
    }

    /**
     * returns the fragment data of the uri, if any, otherwise null.
     *
     * @return null|string
     */
    public function getFragment(): ? string
    {
        return $this->fragment;
    }

    /**
     * creates a new instance of the current exact uri and changes the fragment to
     * the provided fragment string.
     *
     * This method will NOT remove preceding hash tags.
     *
     * @param string $fragment
     * @return UriInterface
     */
    public function withFragment(string $fragment): UriInterface
    {
        $instance = clone $this;
        $instance->fragment = $this->marshalQueryAndFragment($fragment);

        return $instance;
    }

    /**
     * creates a new instance of the current exact uri and changes the fragment by
     * removing any fragment data.
     *
     * @return UriInterface
     */
    public function withoutFragment(): UriInterface
    {
        $instance = clone $this;
        $instance->fragment = null;

        return $instance;
    }

    /**
     * renders the uri based on the currently known data.
     *
     * This method will omit default ports from the uri when they are not explicitly set.
     *
     * @return string
     */
    public function getUri(): string
    {
        $uri = '';

        if ( null !== $this->scheme ) {
            $uri .= $this->scheme.':';
        }

        $authority = $this->getAuthority();

        if ( null !== $authority || $this->scheme === 'file' ) {
            $uri .= '//'.$authority;
        }

        $uri .= $this->path;

        if ( null !== $this->query ) {
            $uri .= '?'.$this->query;
        }

        if ( null !== $this->fragment ) {
            $uri .= '#'.$this->fragment;
        }

        return $uri;
    }

    /**
     * Uri stringifier.
     *
     * calls usually getUri().
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getUri();
    }

    /**
     * creates an uri from a provided parts array.
     *
     * @param array $parts
     * @return UriInterface
     */
    public static function fromParts(array $parts): UriInterface
    {
        $uri = new static();
        $uri->marshalInstance($parts);

        return $uri;
    }

    /**
     * creates an uri from a provided file info object.
     *
     * @param \SplFileInfo $fileInfo
     * @return UriInterface
     */
    public static function fromFileObject(\SplFileInfo $fileInfo): UriInterface
    {
        $parts = [
            'scheme' => 'file',
            'host' => '',
            'path' => $fileInfo->getPathname()
        ];

        return static::fromParts($parts);
    }

    /**
     * marshals the instance of this uri.
     *
     * @param array $parts
     */
    protected function marshalInstance(array $parts)
    {
        $this->scheme = array_key_exists('scheme', $parts)
            ? $this->marshalScheme($parts['scheme'])
            : null
        ;

        $this->userInfo = $parts['user'] ?? null;

        if ( array_key_exists('pass', $parts) ) {
            $this->userInfo .= ':'.$parts['pass'];
        }

        $this->host = array_key_exists('host', $parts)
            ? $this->marshalHost($parts['host'])
            : null
        ;

        $this->port = array_key_exists('port', $parts)
            ? $this->marshalPort($parts['port'])
            : null
        ;

        $this->query = array_key_exists('query', $parts)
            ? $this->marshalQueryAndFragment($parts['query'])
            : null
        ;

        $this->fragment = array_key_exists('fragment', $parts)
            ? $this->marshalQueryAndFragment($parts['fragment'])
            : null
        ;

        $this->calibratePort();
    }

    /**
     * calibrates the port setup of this uri.
     */
    protected function calibratePort()
    {
        if ( $this->port === null && $this->scheme !== null && array_key_exists($this->scheme, self::DEFAULT_PORTS) ) {
            $this->port = self::DEFAULT_PORTS[$this->scheme];
        }
    }
}