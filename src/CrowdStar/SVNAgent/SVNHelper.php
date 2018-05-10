<?php

namespace CrowdStar\SVNAgent;

use MrRio\ShellWrap;
use MrRio\ShellWrapException;

/**
 * Class SVNHelper
 *
 * @package CrowdStar\SVNAgent
 */
class SVNHelper
{
    /**
     * @param string $path
     * @return bool
     * @see https://stackoverflow.com/a/868068/2752269 Check that an svn repository url does not exist
     */
    public static function pathExists(string $path): bool
    {
        try {
            ShellWrap::svn('ls', $path, '--depth', 'empty');
        } catch (ShellWrapException $e) {
            return false;
        }

        return true;
    }
}
