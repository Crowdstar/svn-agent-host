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

use CrowdStar\SVNAgent\Actions\Export;
use CrowdStar\SVNAgent\Request;
use CrowdStar\Tests\SVNAgent\AbstractSvnTestCase;
use MrRio\ShellWrap;

/**
 * Class ExportTest
 *
 * @package CrowdStar\Tests\SVNAgent\Actions
 */
class ExportTest extends AbstractSvnTestCase
{
    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        self::deletePath('path');
    }

    /**
     * @covers Export::processAction()
     * @group svn-server
     */
    public function testProcessAction()
    {
        $action = $this->getAction();
        $this->createSvnUri($action->getPath());
        ShellWrap::rm('-rf', $action->getSvnDir());

        $response = $action->run()->toArray();

        self::assertSame(
            [
                'success'  => true,
                'path'     => '/path/a/',
                'revision' => $response['revision'],
            ],
            $response,
            'a successful "export" action should return a versioned response back.'
        );
        self::assertInternalType('int', $response['revision'], 'field "revision" must be an int');
        self::assertGreaterThan(0, $response['revision'], 'field "revision" must be a positive int');
    }

    /**
     * @covers Export::processAction()
     * @group svn-server
     */
    public function testProcessActionWithExistingLocalDir()
    {
        $action = $this->getAction();
        $this->createSvnUri($action->getPath());

        $response = $action->run()->toArray();

        self::assertSame(
            [
                'path'    => '/path/a/',
                'success' => false,
                'error'   => sprintf(
                    "Folder '%s' already exists. Please delete that folder first before exporting.",
                    "${_SERVER['HOME']}/svn-agent/svn/path/a/"
                ),
            ],
            $response,
            'SVN export fails because of existing local directory'
        );
    }

    /**
     * @covers Export::processAction()
     * @group svn-server
     */
    public function testProcessActionWithMissingRemoteDir()
    {
        self::assertSame(
            [
                'path'    => '/path/a/',
                'success' => false,
                'error'   => 'URL http://svn-server/svn/project1/path/a/ not exists.',
            ],
            $this->getAction()->run()->toArray(),
            'a successful "export" action should return a versioned response back.'
        );
    }

    /**
     * @return Export
     */
    protected function getAction(): Export
    {
        $requestData = ['data' => ['path' => 'path/a']] + self::getBasicRequestData();

        return (new Export((new Request())->init($requestData)));
    }
}
