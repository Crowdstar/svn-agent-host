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

namespace CrowdStar\Tests\SVNAgent\Responses;

use CrowdStar\SVNAgent\Exceptions\ClientException;
use CrowdStar\SVNAgent\Responses\UpdateResponse;
use CrowdStar\Tests\SVNAgent\TestCase;

/**
 * Class UpdateResponseTest
 *
 * @package CrowdStar\Tests\SVNAgent\Responses
 */
class UpdateResponseTest extends TestCase
{
    /**
     * @return array
     */
    public function dataProcess(): array
    {
        $data = [];

        $output = <<<'EOT'

Updating '.':
At revision 3.

EOT;
        $data[] = [
            [
            ],
            3,
            $output,
            'SVN output that has nothing updated.',
        ];

        // Sample output copied from http://svnbook.red-bean.com/en/1.8/svn.ref.svn.c.update.html
        $output = <<<'EOT'

Updating '.':
A    newdir/README
D    newdir/toggle.c
D    newdir/disclose.c
D    newdir/launch.c
U    foo.c
Updated to revision 30.

EOT;
        $data[] = [
            [
                [
                    'type' => 'A',
                    'file' => 'newdir/README',
                ],
                [
                    'type' => 'D',
                    'file' => 'newdir/toggle.c',
                ],
                [
                    'type' => 'D',
                    'file' => 'newdir/disclose.c',
                ],
                [
                    'type' => 'D',
                    'file' => 'newdir/launch.c',
                ],
                [
                    'type' => 'U',
                    'file' => 'foo.c',
                ],
            ],
            30,
            $output,
            'SVN output that contains deleted, modified and added files.',
        ];

        return $data;
    }

    /**
     * @dataProvider dataProcess
     * @covers UpdateResponse::process()
     * @param array $expectedActions
     * @param int $expectedRevision
     * @param string $output
     * @param string $message
     * @throws ClientException
     */
    public function testProcess(array $expectedActions, int $expectedRevision, string $output, string $message)
    {
        $response = (new UpdateResponse('/dummy/path'))->process($output);
        $this->assertEquals($expectedActions, $response->getActions()->toArray(), ($message . ' (compare actions)'));
        $this->assertEquals($expectedRevision, $response->getRevision(), ($message . ' (compare revisions)'));
    }
}
