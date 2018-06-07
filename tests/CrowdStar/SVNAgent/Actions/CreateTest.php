<?php

namespace CrowdStar\Tests\SVNAgent\Actions;

use CrowdStar\SVNAgent\Actions\AbstractAction;
use CrowdStar\SVNAgent\Actions\Create;
use CrowdStar\SVNAgent\Exceptions\ClientException;
use CrowdStar\SVNAgent\Request;
use CrowdStar\Tests\SVNAgent\AbstractSvnTestCase;

/**
 * Class CreateTest
 *
 * @package CrowdStar\Tests\SVNAgent\Actions
 */
class CreateTest extends AbstractSvnTestCase
{
    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass()
    {
        self::deletePath('path');
    }

    /**
     * @inheritdoc
     */
    public static function tearDownAfterClass()
    {
        // self::deletePath('path');
    }

    /**
     * @return array
     */
    public function dataProcess(): array
    {
        return [
            [
                [
                    'success'  => true,
                    'path'     => '/path/1/',
                    'actions'  => [],
                    'revision' => 'a positive integer value',
                ],
                [
                    'data' => ['path' => 'path/1'],
                ] + $this->getBasicRequestData(),
                'a successful "create" action should return a versioned response back.',
            ],
        ];
    }

    /**
     * @dataProvider dataProcess
     * @covers AbstractAction::run()
     * @covers AbstractAction::process()
     * @covers Create::processAction()
     * @param array $expected
     * @param array $requestData
     * @param string $message
     * @throws ClientException
     * @group svn-server
     */
    public function testProcessAction(array $expected, array $requestData, string $message)
    {
        $response = (new Create((new Request())->init($requestData)))->run()->toArray();

        $this->assertInternalType('int', $response['revision'], 'field "revision" must be an int');
        $this->assertGreaterThan(0, $response['revision'], 'field "revision" must be a positive int');

        $expected['revision'] = $response['revision'];
        $this->assertEquals($expected, $response, $message);
    }
}
