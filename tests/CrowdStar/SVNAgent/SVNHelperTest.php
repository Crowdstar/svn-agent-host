<?php

namespace CrowdStar\Tests\SVNAgent;

use CrowdStar\SVNAgent\SVNHelper;

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
