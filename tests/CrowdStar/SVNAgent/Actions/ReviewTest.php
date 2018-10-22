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
use CrowdStar\SVNAgent\Actions\DummyPathBasedAction;
use CrowdStar\SVNAgent\Actions\Review;
use CrowdStar\SVNAgent\Actions\Update;
use CrowdStar\SVNAgent\Exceptions\ClientException;
use CrowdStar\SVNAgent\Request;
use CrowdStar\Tests\SVNAgent\AbstractSvnTestCase;

/**
 * Class ReviewTest
 *
 * @package CrowdStar\Tests\SVNAgent\Actions
 */
class ReviewTest extends AbstractSvnTestCase
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
     * @covers Review::processAction()
     * @throws ClientException
     * @group svn-server
     */
    public function testProcessAction()
    {
        $this->createSvnUri('/path/1');

        $request = (new Request())->init(['data' => ['path' => 'path/1']] + $this->getBasicRequestData());
        $action  = new DummyPathBasedAction($request);

        $this->assertSame(
            [
                'success'  => true,
                'path'     => '/path/1/',
                'actions'  => [],
            ],
            (new Review($request))->run()->toArray(),
            'nothing changed under given SVN folder'
        );

        $this->addSampleFiles($action->getSvnDir());
        $this->assertSame(
            [
                'success'  => true,
                'path'     => '/path/1/',
                'actions' => [
                    [
                        'type' => '?',
                        'file' => '/root/svn-agent/svn/path/1/dir1',
                    ],
                    [
                        'type' => '?',
                        'file' => '/root/svn-agent/svn/path/1/empty1.txt',
                    ],
                    [
                        'type' => '?',
                        'file' => '/root/svn-agent/svn/path/1/hello1.txt',
                    ],
                ],
            ],
            (new Review($request))->run()->toArray(),
            'new files and folder(s) added under given SVN folder'
        );

        (new Commit($request))->run();
        (new Update($request))->run(); // To checkout a local copy.
        $this->assertSame(
            [
                'success'  => true,
                'path'     => '/path/1/',
                'actions'  => [],
            ],
            (new Review($request))->run()->toArray(),
            'nothing changed after all changes under given SVN folder have been committed'
        );

        $this->updateSvnDir($action->getSvnDir());
        $this->assertSame(
            [
                'success'  => true,
                'path'     => '/path/1/',
                'actions' => [
                    [
                        'type' => '!',
                        'file' => '/root/svn-agent/svn/path/1/dir1/empty2.txt',
                    ],
                    [
                        'type' => 'M',
                        'file' => '/root/svn-agent/svn/path/1/dir1/hello2.txt',
                    ],
                    [
                        'type' => '?',
                        'file' => '/root/svn-agent/svn/path/1/dir1/new2.txt',
                    ],
                    [
                        'type' => '!',
                        'file' => '/root/svn-agent/svn/path/1/empty1.txt',
                    ],
                    [
                        'type' => 'M',
                        'file' => '/root/svn-agent/svn/path/1/hello1.txt',
                    ],
                    [
                        'type' => '?',
                        'file' => '/root/svn-agent/svn/path/1/new1.txt',
                    ],
                ],
            ],
            (new Review($request))->run()->toArray(),
            'files changed under given SVN folder'
        );
    }
}
