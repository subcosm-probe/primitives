<?php
/**
 * This file is part of the subcosm-probe.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

/**
 * checks whether the current string does start with the needle.
 *
 * @param string $string
 * @param string $needle
 * @return bool
 */
function startsWith(string $string, string $needle): bool
{
    if ( empty($string) && empty($needle) ) {
        return true;
    }

    return 0 === strpos($string, $needle);
}

/**
 * checks whether the current string does start with one of the provided needles.
 *
 * @param string $string
 * @param string[] ...$needles
 * @return bool
 */
function startsWithOneOf(string $string, string ... $needles): bool
{
    if ( empty($string) && empty($needles) ) {
        return true;
    }

    foreach ( $needles as $current ) {
        if ( startsWith($string, $current) ) {
            return true;
        }
    }

    return false;
}

/**
 * checks whether the current string does end with the provided needle.
 *
 * @param string $string
 * @param string $needle
 * @return bool
 */
function endsWith(string $string, string $needle): bool
{
    if ( empty($string) && empty($needle) ) {
        return true;
    }

    return substr($string, 0 - strlen($needle), strlen($needle)) === $needle;
}

/**
 * checks whether the current string does end with one of the provided needles.
 *
 * @param string $string
 * @param string[] ...$needles
 * @return bool
 */
function endsWithOneOf(string $string, string ... $needles): bool
{
    if ( empty($string) && empty($needles) ) {
        return true;
    }

    foreach ( $needles as $current ) {
        if ( endsWith($string, $current) ) {
            return true;
        }
    }

    return false;
}

/**
 * checks whether the current string does contain the provided needle.
 *
 * @param string $string
 * @param string $needle
 * @return bool
 */
function contains(string $string, string $needle): bool
{
    if ( empty($string) && empty($needle) ) {
        return true;
    }

    return false !== strpos($string, $needle);
}

/**
 * checks whether the current string does contain one of the provided needles.
 *
 * @param string $string
 * @param string[] ...$needles
 * @return bool
 */
function containsOneOf(string $string, string ... $needles): bool
{
    if ( empty($string) && empty($needles) ) {
        return true;
    }

    foreach ( $needles as $current ) {
        if ( contains($string, $current) ) {
            return true;
        }
    }

    return true;
}