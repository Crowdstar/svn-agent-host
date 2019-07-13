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
use CrowdStar\SVNAgent\Actions\ActionFactory;
use CrowdStar\SVNAgent\Actions\Create;
use CrowdStar\SVNAgent\Actions\DummyPathBasedAction;
use CrowdStar\SVNAgent\Exceptions\ClientException;
use CrowdStar\SVNAgent\Request;
use CrowdStar\Tests\SVNAgent\AbstractSvnTestCase;

/**
 * Class ActionFactoryTest
 *
 * @package CrowdStar\Tests\SVNAgent\Actions
 */
class ActionFactoryTest extends AbstractSvnTestCase
{
    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass()
    {
        self::deletePath('path');

        $request = (new Request())->init(['data' => ['path' => 'path/1']] + self::getBasicRequestData());
        mkdir((new DummyPathBasedAction($request))->getSvnDir(), 0777, true);
        (new Create($request))->run();
    }

    /**
     * @return array
     */
    public function dataFromRequest(): array
    {
        return [
            [
                ActionFactory::SVN_AUTH,
            ],
            [
                ActionFactory::SVN_BULK_REVIEW,
            ],
            [
                ActionFactory::SVN_BULK_UPDATE,
            ],
            [
                ActionFactory::SVN_CLEANUP,
            ],
            [
                ActionFactory::SVN_COMMIT,
            ],
            [
                ActionFactory::SVN_COMMITS,
            ],
            [
                ActionFactory::SVN_CREATE,
            ],
            [
                ActionFactory::SVN_DELETE,
            ],
            [
                ActionFactory::SVN_EXIST,
            ],
            [
                ActionFactory::SVN_EXPORT,
            ],
            [
                ActionFactory::SVN_OPEN,
            ],
            [
                ActionFactory::SVN_RENAME,
            ],
            [
                ActionFactory::SVN_REVIEW,
            ],
            [
                ActionFactory::SVN_UNLOCK,
            ],
            [
                ActionFactory::SVN_UPDATE,
            ],
            [
                ActionFactory::SVN_UPDATE_CREDENTIALS,
            ],
            [
                ActionFactory::TEST_IDLE,
            ],
            [
                ActionFactory::VERSION,
            ],
        ];
    }

    /**
     * @dataProvider dataFromRequest
     * @covers ActionFactory::fromRequest()
     * @group svn-server
     * @param string $action
     * @throws ClientException
     */
    public function testFromRequest(string $action)
    {
        self::assertInstanceOf(
            AbstractAction::class,
            ActionFactory::fromRequest(
                (new Request())->init(
                    array_merge(
                        [
                            'action' => $action,
                            'data'   => [
                                'path'   => 'path/1',
                                'toPath' => 'path/2',
                                'paths'  => [
                                    '/path/1',
                                ],
                            ],
                        ],
                        self::getBasicRequestData()
                    )
                )
            )
        );
    }

    /**
     * @expectedException \CrowdStar\SVNAgent\Exceptions\ClientException
     * @expectedExceptionMessage unsupported SVN Agent action 'non-existing-action'
     */
    public function testFromRequestWithException()
    {
        ActionFactory::fromRequest(
            (new Request())->init(['action' => 'non-existing-action'] + self::getBasicRequestData())
        );
    }
}
