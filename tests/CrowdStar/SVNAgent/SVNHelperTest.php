<?php

namespace CrowdStar\Tests\SVNAgent;

use CrowdStar\SVNAgent\Request;
use CrowdStar\SVNAgent\SVNHelper;

/**
 * Class SVNHelperTest
 *
 * @package CrowdStar\Tests\SVNAgent
 */
class SVNHelperTest extends AbstractSvnTestCase
{
    /**
     * @return array
     */
    public function dataUrlExists(): array
    {
        return [
            [
                false,
                'http://example.com/',
                'not even a SVN path',
            ],
            [
                true,
                'http://127.0.0.1/svn/project1',
                'root directory of the SVN repository',
            ],
            [
                false,
                'http://127.0.0.1/svn/project1/directory-not-exist/',
                'a non-existing path under the SVN repository',
            ],
        ];
    }

    /**
     * @dataProvider dataUrlExists
     * @covers SVNHelper::urlExists()
     * @param bool $expected
     * @param string $path
     * @param string $message
     */
    public function testUrlExists(bool $expected, string $path, string $message)
    {
        $this->assertSame(
            $expected,
            SVNHelper::urlExists($path, (new Request())->init(self::getBasicRequestData())),
            $message
        );
    }
}
