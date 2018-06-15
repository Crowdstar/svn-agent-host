<?php

namespace CrowdStar\Tests\SVNAgent;

use CrowdStar\SVNAgent\Actions\Create;
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
     * @inheritdoc
     */
    public static function setUpBeforeClass()
    {
        self::deletePath('path');
    }

    /**
     * @return array
     */
    public function dataGetSvnVersion(): array
    {
        return [
            [
                '1.10.0',
                'svn, version 1.10.0 (r1827917)',
                '',
            ],
            [
                '1.8.8',
                'svn, version 1.8.8 (r1568071)',
                '',
            ],
        ];
    }

    /**
     * @dataProvider dataGetSvnVersion
     * @covers SVNHelper::getSvnVersion()
     * @param string $expected
     * @param string $rawSvnVersion
     * @param string $message
     */
    public function testGetSvnVersion(string $expected, string $rawSvnVersion, string $message)
    {
        /** @var SVNHelper $helper */
        $helper = $this
            ->getMockBuilder(SVNHelper::class)
            ->setMethods(['getRawSvnVersion'])
            ->getMock();

        $helper->method('getRawSvnVersion')->willReturn($rawSvnVersion);

        $this->assertSame($expected, $helper->getSvnVersion(), $message);
    }

    /**
     * @covers SVNHelper::getUrl()
     */
    public function testGetUrl()
    {
        $action = (new Create((new Request())->init(['data' => ['path' => 'path/1'],] + $this->getBasicRequestData())));
        $action->run();
        $this->assertSame('http://127.0.0.1/svn/project1/path/1', (new SVNHelper())->getUrl($action->getSvnDir()));

        self::deletePath('path');
    }

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
