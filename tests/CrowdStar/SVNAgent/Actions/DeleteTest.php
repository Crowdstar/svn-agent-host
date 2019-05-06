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
use CrowdStar\SVNAgent\Actions\Delete;
use CrowdStar\SVNAgent\Request;
use CrowdStar\Tests\SVNAgent\AbstractSvnTestCase;
use MrRio\ShellWrap;

/**
 * Class DeleteTest
 *
 * @package CrowdStar\Tests\SVNAgent\Actions
 */
class DeleteTest extends AbstractSvnTestCase
{
    /**
     * @var string
     */
    protected $path = 'path/1';

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();

        self::deletePath('path');
    }

    /**
     * @covers AbstractAction::run()
     * @covers AbstractAction::process()
     * @covers Delete::processAction()
     * @group svn-server
     */
    public function testProcessActionWhereBothLocalDirAndRemoteDirNotExist()
    {
        $this->assertSame(
            [
                'success' => true,
                'message' => '',
            ],
            (new Delete((new Request())->init($this->getRequestData())))->run()->toArray()
        );
    }


    /**
     * @covers AbstractAction::run()
     * @covers AbstractAction::process()
     * @covers Delete::processAction()
     * @group svn-server
     */
    public function testProcessActionWhereBothLocalDirAndRemoteDirExist()
    {
        $this->createSvnUri($this->path);
        $this->assertTrue((new Delete((new Request())->init($this->getRequestData())))->run()->toArray()['success']);
    }

    /**
     * @covers AbstractAction::run()
     * @covers AbstractAction::process()
     * @covers Delete::processAction()
     * @group svn-server
     */
    public function testProcessActionWhereLocalExistsOnly()
    {
        $this->mkdir($this->path);
        $response = (new Delete((new Request())->init($this->getRequestData())))->run()->toArray();
        $this->assertSame(['success'  => true, 'message' => 'local folder deleted'], $response);
    }

    /**
     * @covers AbstractAction::run()
     * @covers AbstractAction::process()
     * @covers Delete::processAction()
     * @group svn-server
     */
    public function testProcessActionWhereRemoteExistsOnly()
    {
        $this->createSvnUri($this->path);
        $action = (new Delete((new Request())->init($this->getRequestData())));
        ShellWrap::rm('-rf', $action->getSvnDir());
        $this->assertTrue($action->run()->toArray()['success']);
    }

    /**
     * @return array
     */
    protected function getRequestData(): array
    {
        return ['data' => ['path' => $this->path]] + self::getBasicRequestData();
    }
}
