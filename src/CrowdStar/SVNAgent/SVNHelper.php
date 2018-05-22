<?php

namespace CrowdStar\SVNAgent;

use CrowdStar\SVNAgent\Exceptions\Exception;
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
            ShellWrap::svn('info', $path);
        } catch (ShellWrapException $e) {
            return false;
        }

        return true;
    }

    /**
     * @param string $url
     * @param Request $request
     * @return bool
     * @see https://stackoverflow.com/a/868068/2752269 Check that an svn repository url does not exist
     */
    public static function urlExists(string $url, Request $request): bool
    {
        try {
            ShellWrap::svn(
                'info',
                $url,
                [
                    'username' => $request->getUsername(),
                    'password' => $request->getPassword(),
                ]
            );
        } catch (ShellWrapException $e) {
            return false;
        }

        return true;
    }

    /**
     * @param string $url1
     * @param string $url2
     * @return bool
     */
    public static function sameUrl(string $url1, string $url2): bool
    {
        return (PathHelper::rtrim($url1) == PathHelper::rtrim($url2));
    }

    /**
     * @param string $dir
     * @return string
     * @throws Exception
     */
    public static function getUrl(string $dir): string
    {
        if (is_dir($dir)) {
            $oldDir = getcwd();
            chdir($dir);

            $sh = new ShellWrap();
            try {
                ShellWrap::svn('info', '--show-item', 'url');
            } catch (ShellWrapException $e) {
                throw new Exception("unable to fetch SVN information from directory {$dir}");
            }

            chdir($oldDir);

            return trim((string) $sh);
        } else {
            throw new Exception("directory '{$dir}' not exists");
        }
    }
}
