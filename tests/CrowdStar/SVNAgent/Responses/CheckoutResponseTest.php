<?php

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
