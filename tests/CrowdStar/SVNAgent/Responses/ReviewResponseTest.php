<?php

namespace CrowdStar\Tests\SVNAgent\Responses;

use CrowdStar\SVNAgent\Exceptions\ClientException;
use CrowdStar\SVNAgent\Responses\ReviewResponse;
use PHPUnit\Framework\TestCase;

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
