<?php

namespace CrowdStar\Tests\SVNAgent\Actions;

use CrowdStar\SVNAgent\Actions\AbstractAction;
use CrowdStar\SVNAgent\Actions\Create;
use CrowdStar\SVNAgent\Exceptions\ClientException;
use CrowdStar\SVNAgent\Request;
use CrowdStar\Tests\SVNAgent\AbstractSvnTestCase;

/**
 * Class InvalidSvnServerTest
 *
 * @package CrowdStar\Tests\SVNAgent\Actions
 */
class InvalidSvnServerTest extends AbstractSvnTestCase
{
    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::setUpInvalidSvnHost();
    }

    /**
     * @inheritdoc
     */
    public static function tearDownAfterClass()
    {
        self::resetSvnHost();
        parent::tearDownAfterClass();
    }

    /**
     * @return array
     */
    public function dataProcess(): array
    {
        return [
            [
                [
                    'success'  => false,
                    'error'    => <<<EOT
svn: E170013: Unable to connect to a repository at URL 'http://127.0.0.1/path/1'
svn: E175009: The XML response contains invalid XML
svn: E130003: Malformed XML: no element found at line 1
EOT
                    ,
                    'path'     => '/path/1/',
                ],
                [
                    'data' => ['path' => 'path/1'],
                ] + $this->getBasicRequestData(),
                '',
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
        $this->assertEquals($expected, (new Create((new Request())->init($requestData)))->run()->toArray(), $message);
    }
}
