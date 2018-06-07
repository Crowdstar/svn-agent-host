<?php

namespace CrowdStar\Tests\SVNAgent;

use CrowdStar\SVNAgent\Config;

/**
 * Class AbstractInvalidSvnServerTestCase
 *
 * @package CrowdStar\Tests\SVNAgent
 */
abstract class AbstractInvalidSvnServerTestCase extends AbstractSvnTestCase
{
    /**
     * @var string
     */
    protected static $svnRoot;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$svnRoot = getenv(Config::SVN_AGENT_SVN_ROOT, '');
        putenv(Config::SVN_AGENT_SVN_ROOT . '=' . 'http://a-non-existing-server-akjozonh9y3z9vyq.com');
    }

    public static function tearDownAfterClass()
    {
        putenv(Config::SVN_AGENT_SVN_ROOT . '=' . self::$svnRoot);
        parent::tearDownAfterClass();
    }
}
