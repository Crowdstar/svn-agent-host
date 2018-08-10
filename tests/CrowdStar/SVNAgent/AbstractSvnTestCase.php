<?php

namespace CrowdStar\Tests\SVNAgent;

use CrowdStar\SVNAgent\Actions\Open;
use CrowdStar\SVNAgent\Config;
use CrowdStar\SVNAgent\Exceptions\ClientException;
use CrowdStar\SVNAgent\Request;
use CrowdStar\SVNAgent\SVNHelper;
use MrRio\ShellWrap;

/**
 * Class AbstractSvnTestCase
 *
 * @package CrowdStar\Tests\SVNAgent
 */
abstract class AbstractSvnTestCase extends TestCase
{
    /**
     * To store original value of environment variable Config::SVN_AGENT_SVN_ROOT.
     * @var string
     */
    protected static $svnRoot;

    /**
     * @return void
     * @see AbstractSvnTestCase::resetSvnHost()
     */
    public static function setUpInvalidSvnHost()
    {
        self::$svnRoot = getenv(Config::SVN_AGENT_SVN_ROOT, '');
        // Change SVN root from "http://example.com/svn/project1" to "http://example.com" (domain name may vary here).
        putenv(Config::SVN_AGENT_SVN_ROOT . '=' . dirname(Config::singleton()->getSvnRoot(), 2));
    }

    /**
     * @return void
     * @see AbstractSvnTestCase::resetSvnHost()
     */
    public static function setUpUnknownSvnHost()
    {
        self::$svnRoot = getenv(Config::SVN_AGENT_SVN_ROOT, '');
        putenv(Config::SVN_AGENT_SVN_ROOT . '=' . 'http://t6dkr8gkvc6o8bvf97.test');
    }

    /**
     * @return void
     * @see AbstractSvnTestCase::setUpInvalidSvnHost()
     * @see AbstractSvnTestCase::setUpUnknownSvnHost()
     */
    public static function resetSvnHost()
    {
        putenv(Config::SVN_AGENT_SVN_ROOT . '=' . self::$svnRoot);
    }

    /**
     * @param string $path
     * @throws ClientException
     */
    protected static function deletePath(string $path)
    {
        // Change directory first so that current directory is always valid.
        chdir($_SERVER['HOME']);

        $request = (new Request())->init(self::getBasicRequestData() + ['data' => ['path' => $path]]);
        $action  = new Open($request);

        if (is_dir($action->getSvnDir())) {
            ShellWrap::rm('-rf', $action->getSvnDir());
        }
        if (SVNHelper::urlExists($action->getSvnUri(), $request)) {
            ShellWrap::svn(
                'delete',
                $action->getSvnUri(),
                [
                    'username' => $request->getUsername(),
                    'password' => $request->getPassword(),
                    'm'        => 'path deleted',
                ]
            );
        }
    }

    /**
     * @param string ...$paths
     * @throws ClientException
     */
    protected static function deletePaths(string ...$paths)
    {
        foreach ($paths as $path) {
            self::deletePath($path);
        }
    }

    /**
     * @param string $path
     * @return Request
     */
    protected function getPathBasedRequest(string $path): Request
    {
        return (new Request())->init(['data' => ['path' => $path]] + $this->getBasicRequestData());
    }

    /**
     * @param string ...$paths
     * @return Request
     */
    protected function getPathsBasedRequest(string ...$paths): Request
    {
        return (new Request())->init(['data' => ['paths' => $paths]] + $this->getBasicRequestData());
    }

    /**
     * @return array
     */
    protected static function getBasicRequestData(): array
    {
        return [
            'username' => base64_encode(self::getSvnUsername()),
            'password' => base64_encode(self::getSvnPassword()),
            'timeout'  => 30,
        ];
    }

    /**
     * @return array
     */
    protected static function getBasicRequestDataWithIncorrectCredentials(): array
    {
        return [
            'username' => uniqid() . '-',
            'password' => uniqid() . '-',
            'timeout'  => 30,
        ];
    }

    /**
     * @return string
     */
    protected static function getSvnUsername(): string
    {
        return $_ENV['SVN_USERNAME'];
    }

    /**
     * @return string
     */
    protected static function getSvnPassword(): string
    {
        return $_ENV['SVN_PASSWORD'];
    }
}
