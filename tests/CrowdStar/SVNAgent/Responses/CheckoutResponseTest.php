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
use CrowdStar\SVNAgent\Responses\CheckoutResponse;
use CrowdStar\Tests\SVNAgent\TestCase;

/**
 * Class CheckoutResponseTest
 *
 * @package CrowdStar\Tests\SVNAgent\Responses
 */
class CheckoutResponseTest extends TestCase
{
    /**
     * @return array
     */
    public function dataProcess(): array
    {
        $data = [];

        $output = <<<'EOT'

Checked out revision 19.

EOT;
        $data[] = [
            [
            ],
            19,
            $output,
            'SVN output that has nothing checked out.',
        ];

        $output = <<<'EOT'

A    1/a
A    1/b
A    1/c
Checked out revision 20.

EOT;
        $data[] = [
            [
                [
                    'type' => 'A',
                    'file' => '1/a',
                ],
                [
                    'type' => 'A',
                    'file' => '1/b',
                ],
                [
                    'type' => 'A',
                    'file' => '1/c',
                ],
            ],
            20,
            $output,
            'SVN output that contains files checked out.',
        ];

        return $data;
    }

    /**
     * @dataProvider dataProcess
     * @covers CheckoutResponse::process()
     * @param array $expectedActions
     * @param int $expectedRevision
     * @param string $output
     * @param string $message
     * @throws ClientException
     */
    public function testProcess(array $expectedActions, int $expectedRevision, string $output, string $message)
    {
        $repsonse = (new CheckoutResponse('/dummy/path'))->process($output);
        $this->assertEquals($expectedActions, $repsonse->getActions()->toArray(), ($message . ' (compare actions)'));
        $this->assertEquals($expectedRevision, $repsonse->getRevision(), ($message . ' (compare revisions)'));
    }
}
