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
        putenv(Config::SVN_AGENT_SVN_ROOT . '=' . 'http://127.0.0.1');
    }

    /**
     * @return void
     * @see AbstractSvnTestCase::resetSvnHost()
     */
    public static function setUpUnknownSvnHost()
    {
        self::$svnRoot = getenv(Config::SVN_AGENT_SVN_ROOT, '');
        putenv(Config::SVN_AGENT_SVN_ROOT . '=' . 'http://t6dkr8gkvc6o8bvf97.com');
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
