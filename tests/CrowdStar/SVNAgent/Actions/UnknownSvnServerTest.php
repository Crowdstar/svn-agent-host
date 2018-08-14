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

namespace CrowdStar\Tests\SVNAgent\Actions;

use CrowdStar\SVNAgent\Actions\AbstractAction;
use CrowdStar\SVNAgent\Actions\Create;
use CrowdStar\SVNAgent\Exceptions\ClientException;
use CrowdStar\SVNAgent\Request;
use CrowdStar\Tests\SVNAgent\AbstractSvnTestCase;

/**
 * Class InvalidSvnServerTest
 *
 * @package CrowdStar\Tests\SVNAgent\Actions
 */
class UnknownSvnServerTest extends AbstractSvnTestCase
{
    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::setUpUnknownSvnHost();
    }

    /**
     * @inheritdoc
     */
    public static function tearDownAfterClass()
    {
        self::resetSvnHost();
        parent::tearDownAfterClass();
    }

    /**
     * @return array
     */
    public function dataProcess(): array
    {
        /**
         * SVN responses:
         * 1. from version 1.10.0 on Mac:
         *    svn: E170013: Unable to connect to a repository at URL 'http://t6dkr8gkvc6o8bvf97.test/path/1'
         *    svn: E670008: nodename nor servname provided, or not known
         * 2. from version 1.8.8 on Travis CI:
         *    svn: E670002: Unable to connect to a repository at URL 'http://t6dkr8gkvc6o8bvf97.test/path/1'
         *    svn: E670002: Name or service not known
         */
        return [
            [
                [
                    'success'  => false,
                    'error'    => "Unable to connect to a repository at URL 'http://t6dkr8gkvc6o8bvf97.test/path/1'\n",
                    'path'     => '/path/1/',
                ],
                [
                    'data' => ['path' => 'path/1'],
                ] + $this->getBasicRequestData(),
                '',
            ],
        ];
    }

    /**
     * @dataProvider dataProcess
     * @covers AbstractAction::run()
     * @covers AbstractAction::process()
     * @covers Create::processAction()
     * @param array $expected
     * @param array $requestData
     * @throws ClientException
     * @group svn-server
     */
    public function testProcessAction(array $expected, array $requestData)
    {
        $response = (new Create((new Request())->init($requestData)))->run()->toArray();
        foreach (['success', 'path'] as $field) {
            $this->assertSame($expected[$field], $response[$field]);
        }
        $this->assertContains($expected['error'], $response['error']);
    }
}
