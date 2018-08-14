<?php
/**************************************************************************
 * Copyright 2018 Glu Mobile Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *************************************************************************/

namespace CrowdStar\Tests\SVNAgent;

use CrowdStar\SVNAgent\Actions\Create;
use CrowdStar\SVNAgent\Config;
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
        $this->assertSame(
            Config::singleton()->getSvnRoot() . '/path/1',
            (new SVNHelper())->getUrl($action->getSvnDir())
        );

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
                Config::singleton()->getSvnRoot(),
                'root directory of the SVN repository',
            ],
            [
                false,
                Config::singleton()->getSvnRoot() . '/directory-not-exist/',
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
