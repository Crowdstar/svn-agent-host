<?php

namespace CrowdStar\Tests\SVNAgent\Actions;

use CrowdStar\SVNAgent\Actions\AbstractAction;
use CrowdStar\SVNAgent\Actions\Update;
use CrowdStar\SVNAgent\Exceptions\ClientException;
use CrowdStar\SVNAgent\Request;
use CrowdStar\Tests\SVNAgent\AbstractSvnTestCase;

/**
 * Class UpdateTest
 *
 * @package CrowdStar\Tests\SVNAgent\Actions
 */
class UpdateTest extends AbstractSvnTestCase
{
    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass()
    {
        self::deletePath('path');
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
                    'path'     => '/path/a/',
                    'actions'  => [],
                    'revision' => 'a positive integer value',
                ],
                [
                    'data' => ['path' => 'path/a'],
                ] + $this->getBasicRequestData(),
                'a successful "update" action should return a versioned response back.',
            ],
        ];
    }

    /**
     * @dataProvider dataProcess
     * @covers AbstractAction::run()
     * @covers AbstractAction::process()
     * @covers Update::processAction()
     * @param array $expected
     * @param array $requestData
     * @param string $message
     * @throws ClientException
     * @group svn-server
     */
    public function testProcessAction(array $expected, array $requestData, string $message)
    {
        $response = (new Update((new Request())->init($requestData)))->run()->toArray();

        $this->assertInternalType('int', $response['revision'], 'field "revision" must be an int');
        $this->assertGreaterThan(0, $response['revision'], 'field "revision" must be a positive int');

        $expected['revision'] = $response['revision'];
        $this->assertEquals($expected, $response, $message);
    }

    /**
     * @covers AbstractAction::run()
     * @covers AbstractAction::process()
     * @covers Update::processAction()
     * @group svn-server
     */
    public function testProcessActionWithIncorrectCredentials()
    {
        $action = new Update((new Request())->init(
            ['data' => ['path' => 'path/incorrect-credentials']] + $this->getBasicRequestDataWithIncorrectCredentials()
        ));
        $response = $action->run()->toArray();

        $this->assertArraySubset(
            [
                'path'     => '/path/incorrect-credentials/',
                'success'  => false,
            ],
            $response,
            'SVN update fails because of invalid credentials'
        );
    }

    /**
     * @covers AbstractAction::run()
     * @covers AbstractAction::process()
     * @covers Update::processAction()
     * @group svn-server
     */
    public function testProcessActionWithInvalidWorkingCopy()
    {
        $action1 = new Update((new Request())->init(
            ['data' => ['path' => 'path/1']] + $this->getBasicRequestData()
        ));
        $action2 = new Update((new Request())->init(
            ['data' => ['path' => 'path/2']] + $this->getBasicRequestData()
        ));

        $action1->run()->toArray();
        rename($action1->getSvnDir(), $action2->getSvnDir());

        $this->assertSame(
            [
                'path'    => '/path/2/',
                'success' => false,
                'error'   => implode(
                    '',
                    [
                        "Folder '{$_SERVER['HOME']}/svn-agent/svn/path/2/' points to SVN URL ",
                        "http://127.0.0.1/svn/project1/path/1 which is different from expected URL ",
                        "http://127.0.0.1/svn/project1/path/2/",
                    ]
                ),
            ],
            $action2->run()->toArray(),
            'path "/path/2" has wrong working copy under it for path "/path/1"'
        );
    }
}
