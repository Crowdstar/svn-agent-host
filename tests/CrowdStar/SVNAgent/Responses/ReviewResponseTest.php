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
use CrowdStar\SVNAgent\Responses\ReviewResponse;
use CrowdStar\Tests\SVNAgent\TestCase;

/**
 * Class ReviewResponseTest
 *
 * @package CrowdStar\Tests\SVNAgent\Responses
 */
class ReviewResponseTest extends TestCase
{
    /**
     * @return array
     */
    public function dataProcess(): array
    {
        $output = <<<'EOT'

!       a
M       b    
    ?       c

EOT;

        return [
            [
                [
                    [
                        'type' => '!',
                        'file' => 'a',
                    ],
                    [
                        'type' => 'M',
                        'file' => 'b',
                    ],
                    [
                        'type' => '?',
                        'file' => 'c',
                    ],
                ],
                $output,
                'a simple SVN status output that contains deleted, modified and added files.',
            ],
        ];
    }

    /**
     * @dataProvider dataProcess
     * @covers ReviewResponse::process()
     * @param array $expected
     * @param string $output
     * @param string $message
     * @throws ClientException
     */
    public function testProcess(array $expected, string $output, string $message)
    {
        $this->assertEquals(
            $expected,
            (new ReviewResponse('/dummy/path'))->process($output)->getActions()->toArray(),
            $message
        );
    }
}
