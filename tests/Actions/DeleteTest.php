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
use CrowdStar\SVNAgent\Actions\Commit;
use CrowdStar\SVNAgent\Actions\Delete;
use CrowdStar\SVNAgent\Actions\Update;
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
        $request = (new Request())->init($this->getRequestData());
        $action  = new Delete($request);

        $this->createSvnUri($action->getPath());
        $this->addSampleFiles($action->getSvnDir());
        (new Commit($request))->run();
        (new Update($request))->run(); // To checkout a local copy.

        $response = $action->run()->toArray();
        $backupDir = str_replace('local files moved to folder ', '', $response['message']);

        $this->assertTrue($response['success']);
        $this->assertFileExists("{$backupDir}/empty1.txt");
        $this->assertDirectoryExists("{$backupDir}/dir2");
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

        $this->assertTrue($response['success']);
        $this->assertContains('local files moved to folder ', $response['message']);
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
