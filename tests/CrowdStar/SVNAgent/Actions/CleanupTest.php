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
use CrowdStar\SVNAgent\Actions\Cleanup;
use CrowdStar\SVNAgent\Actions\Commit;
use CrowdStar\SVNAgent\Actions\DummyPathBasedAction;
use CrowdStar\SVNAgent\Actions\Review;
use CrowdStar\SVNAgent\Actions\Update;
use CrowdStar\SVNAgent\Exceptions\ClientException;
use CrowdStar\SVNAgent\Request;
use CrowdStar\Tests\SVNAgent\AbstractSvnTestCase;

/**
 * Class CleanupTest
 *
 * @package CrowdStar\Tests\SVNAgent\Actions
 */
class CleanupTest extends AbstractSvnTestCase
{
    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass()
    {
        self::deletePath('path');
    }

    /**
     * @covers AbstractAction::run()
     * @covers AbstractAction::process()
     * @covers Cleanup::processAction()
     * @see ReviewTest::testProcessAction() Both test cases are similar to each other with same data in different steps
     * @throws ClientException
     * @group svn-server
     */
    public function testProcessAction()
    {
        $this->createSvnUri('/path/1');

        $request = (new Request())->init(['data' => ['path' => 'path/1']] + $this->getBasicRequestData());
        $action  = new DummyPathBasedAction($request);
        $expectedResponse = [
            'success' => true,
            'message' => '',
        ];

        $this->assertCount(
            0,
            (new Review($request))->run()->toArray()['actions'],
            'nothing changed since no files or changes under given SVN folder'
        );
        $this->assertSame(
            $expectedResponse,
            (new Cleanup($request))->run()->toArray(),
            'nothing to clean up since no files or changes under given SVN folder'
        );

        $this->addSampleFiles($action->getSvnDir());
        $this->assertCount(
            3,
            (new Review($request))->run()->toArray()['actions'],
            '2 new files and 1 new folder added under given SVN folder'
        );
        $this->assertSame(
            $expectedResponse,
            (new Cleanup($request))->run()->toArray(),
            'nothing to clean up since new files added have been cleaned up under given SVN folder'
        );
        $this->assertCount(
            0,
            (new Review($request))->run()->toArray()['actions'],
            'nothing changed after being cleaned up under given SVN folder'
        );
        $this->addSampleFiles($action->getSvnDir()); // Put new files back.

        (new Commit($request))->run();
        (new Update($request))->run(); // To checkout a local copy.
        $this->assertCount(
            0,
            (new Review($request))->run()->toArray()['actions'],
            'nothing changed after all changes under given SVN folder have been committed'
        );
        $this->assertSame(
            $expectedResponse,
            (new Cleanup($request))->run()->toArray(),
            'nothing to clean up since new files have been committed under given SVN folder'
        );

        $this->makeChangesUnderSvnDir($action->getSvnDir());
        $this->assertCount(
            6,
            (new Review($request))->run()->toArray()['actions'],
            '6 files added/changed/deleted under given SVN folder'
        );
        $this->assertSame(
            $expectedResponse,
            (new Cleanup($request))->run()->toArray(),
            'nothing to clean up since files added/changed/deleted have been cleaned up under given SVN folder'
        );
        $this->assertCount(
            0,
            (new Review($request))->run()->toArray()['actions'],
            'nothing added/changed/deleted after being cleaned up under given SVN folder'
        );
        $this->makeChangesUnderSvnDir($action->getSvnDir()); // Put changes back.
    }
}
