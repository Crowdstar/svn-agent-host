<?php

namespace CrowdStar\Tests\SVNAgent;

use CrowdStar\SVNAgent\SVNHelper;
use PHPUnit\Framework\TestCase;

/**
 * Class SVNHelperTest
 *
 * @package CrowdStar\Tests\SVNAgent
 */
class SVNHelperTest extends TestCase
{
    /**
     * @return array
     */
    public function dataPathExists(): array
    {
        return [
            [
                false,
                'http://example.com/',
                'not even a SVN path',
            ],
            [
                true,
                'https://github.com/Crowdstar/svn-agent-host/',
                'root directory of the SVN repository',
            ],
            [
                true,
                'https://github.com/Crowdstar/svn-agent-host/trunk/',
                'branch trunk of the SVN repository',
            ],
            [
                false,
                'https://github.com/Crowdstar/svn-agent-host/trunk/directory-not-exist/',
                'a non-existing path under the SVN repository',
            ],
        ];
    }

    /**
     * @dataProvider dataPathExists
     * @covers SVNHelper::pathExists()
     * @param bool $expected
     * @param string $path
     * @param string $message
     */
    public function testPathExists(bool $expected, string $path, string $message)
    {
        $this->assertSame($expected, SVNHelper::pathExists($path), $message);
    }
}
