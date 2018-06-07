<?php

namespace CrowdStar\Tests\SVNAgent\Actions;

use CrowdStar\SVNAgent\Actions\AbstractAction;
use CrowdStar\SVNAgent\Actions\Create;
use CrowdStar\SVNAgent\Exceptions\ClientException;
use CrowdStar\SVNAgent\Request;
use CrowdStar\SVNAgent\Responses\PathBasedResponse;
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
        /**
         * SVN responses:
         * 1. from 1.10.0 on Mac:
         *    svn: E170013: Unable to connect to a repository at URL 'http://127.0.0.1/path/1'
         *    svn: E175009: The XML response contains invalid XML
         *    svn: E130003: Malformed XML: no element found at line 1
         * 2. from Travis CI:
         *    svn: E175002: Unexpected HTTP status 405 'Method Not Allowed' on '/path/1'
         *
         *    svn: E175002: Additional errors:
         *    svn: E175002: PROPFIND request on '/path/1' failed: 405 Method Not Allowed
         */
        return [
            [
                [
                    'success'  => false,
                    'error'    => '/: (Unable to connect to a repository at URL|Unexpected HTTP status 405)/',
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
     * @throws ClientException
     * @group svn-server
     */
    public function testProcessAction(array $expected, array $requestData)
    {
        /** @var PathBasedResponse $response */
        $response = (new Create((new Request())->init($requestData)))->run()->toArray();
        foreach (['success', 'path'] as $field) {
            $this->assertSame($expected[$field], $response[$field]);
        }
        $this->assertRegExp($expected['error'], $response['error']);
    }
}
