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
     * @return string
     */
    public function getSvnVersion(): string
    {
        return preg_replace('/^.*\s+version\s+([\d\.]+)\s+\(.+$/', '$1', $this->getRawSvnVersion());
    }

    /**
     * @return string
     */
    protected function getRawSvnVersion(): string
    {
        // Output of command "svn --version | sed -n 1p":
        //     1. on macOS High Sierra: svn, version 1.10.0 (r1827917)
        //     2. in Travis CI:         svn, version 1.8.8 (r1568071)
        return trim(ShellWrap::svn('--version | sed -n 1p'));
    }

    /**
     * @param string $dir
     * @return string
     * @throws Exception
     */
    public function getUrl(string $dir): string
    {
        if (is_dir($dir)) {
            $sh = new ShellWrap();
            try {
                ShellWrap::svn("info '{$dir}' | grep '^URL: ' | awk '{print \$NF}'");
            } catch (ShellWrapException $e) {
                throw new Exception("unable to fetch SVN information from directory {$dir} " . $e->getMessage());
            }

            return trim((string) $sh);
        } else {
            throw new Exception("directory '{$dir}' not exists");
        }
    }

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
}
