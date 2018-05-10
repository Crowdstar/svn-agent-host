<?php

namespace CrowdStar\SVNAgent;

/**
 * Class PathHelper
 *
 * @package CrowdStar\SVNAgent
 */
class PathHelper
{
    /**
     * @param string $path
     * @return string
     */
    public static function trim(string $path): string
    {
        return trim($path, DIRECTORY_SEPARATOR);
    }

    /**
     * @param string $path
     * @return string
     */
    public static function ltrim(string $path): string
    {
        return ltrim($path, DIRECTORY_SEPARATOR);
    }

    /**
     * @param string $path
     * @return string
     */
    public static function rtrim(string $path): string
    {
        return rtrim($path, DIRECTORY_SEPARATOR);
    }
}
