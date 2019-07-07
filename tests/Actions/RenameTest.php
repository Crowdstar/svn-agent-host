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
use CrowdStar\SVNAgent\Actions\Create;
use CrowdStar\SVNAgent\Actions\DummyPathBasedAction;
use CrowdStar\SVNAgent\Actions\Rename;
use CrowdStar\SVNAgent\Exceptions\ClientException;
use CrowdStar\SVNAgent\Request;
use CrowdStar\SVNAgent\SVNHelper;
use CrowdStar\Tests\SVNAgent\AbstractSvnTestCase;
use MrRio\ShellWrap;

/**
 * Class RenameTest
 *
 * @package CrowdStar\Tests\SVNAgent\Actions
 */
class RenameTest extends AbstractSvnTestCase
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
                    'revision' => 'a positive integer value',
                ],
                [
                    'data' => [
                        'path'   => '/path/1/',
                        'toPath' => '/path/2/',
                    ],
                ] + self::getBasicRequestData(),
                'a successful "rename" action should return a versioned response back.',
            ],
        ];
    }

    /**
     * @dataProvider dataProcess
     * @covers AbstractAction::run()
     * @covers AbstractAction::process()
     * @covers Rename::processAction()
     * @param array $expected
     * @param array $requestData
     * @param string $message
     * @throws ClientException
     * @throws \CrowdStar\SVNAgent\Exceptions\Exception
     * @group svn-server
     */
    public function testProcessAction(array $expected, array $requestData, string $message)
    {
        $request     = (new Request())->init($requestData);
        $dummyAction = new DummyPathBasedAction($request);
        mkdir($dummyAction->getSvnDir(), 0777, true);
        (new Create($request))->run();
        $renameAction = new Rename($request);

        $this->assertTrue(SVNHelper::urlExists($renameAction->getSvnUri(), $request), 'source URL should exist');
        $this->assertTrue(SVNHelper::pathExists($renameAction->getSvnDir()), 'source folder should exist');
        $this->assertSame(
            ((new SVNHelper())->getUrl($renameAction->getSvnDir()) . DIRECTORY_SEPARATOR),
            $renameAction->getSvnUri(),
            'source folder should have correct SVN URL in use'
        );
        $this->assertFalse(
            SVNHelper::urlExists($renameAction->getToAction()->getSvnUri(), $request),
            'destination URL should not exist'
        );
        $this->assertFileNotExists($renameAction->getToAction()->getSvnDir(), 'destination folder should not exist');

        $response = $renameAction->run()->toArray();

        $this->assertInternalType('int', $response['revision'], 'field "revision" must be an int');
        $this->assertGreaterThan(0, $response['revision'], 'field "revision" must be a positive int');

        $expected['revision'] = $response['revision'];
        $this->assertSame($expected, $response, $message);

        $this->assertFalse(SVNHelper::urlExists($renameAction->getSvnUri(), $request), 'source URL should not exist');
        $this->assertFalse(SVNHelper::pathExists($renameAction->getSvnDir()), 'source folder should not exist');
        $this->assertSame(
            ((new SVNHelper())->getUrl($renameAction->getToAction()->getSvnDir()) . DIRECTORY_SEPARATOR),
            $renameAction->getToAction()->getSvnUri(),
            'destination folder should have correct SVN URL in use'
        );
        $this->assertTrue(
            SVNHelper::urlExists($renameAction->getToAction()->getSvnUri(), $request),
            'destination URL should exist'
        );
        $this->assertFileExists($renameAction->getToAction()->getSvnDir(), 'destination folder should exist');
    }

    /**
     * @covers Rename::validate()
     * @group svn-server
     * @expectedException \CrowdStar\SVNAgent\Exceptions\ClientException
     * @expectedExceptionMessage source path and destination path are the same
     */
    public function testValidateWhenPathsAreTheSame()
    {
        new Rename(
            (new Request())->init(
                [
                    'data' => [
                        'path'   => '/path/3/',
                        'toPath' => '/path/3',
                    ],
                ] + self::getBasicRequestData()
            )
        );
    }

    /**
     * @covers Rename::validate()
     * @group svn-server
     */
    public function testValidateWhenSourcePathNotExist()
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage(
            "source path '" . self::getDummyPathBasedAction('/path/4/')->getSvnDir() . "' not exist"
        );

        new Rename(
            (new Request())->init(
                [
                    'data' => [
                        'path'   => '/path/4/',
                        'toPath' => '/path/5/',
                    ],
                ] + self::getBasicRequestData()
            )
        );
    }

    /**
     * @covers Rename::validate()
     * @group svn-server
     * @expectedException \CrowdStar\SVNAgent\Exceptions\ClientException
     * @expectedExceptionMessage field 'toPath' not passed in in the request
     */
    public function testValidateWhenFieldToPathMissing()
    {
        $this->mkdir('/path/3/');

        new Rename(
            $request = (new Request())->init(
                [
                    'data' => [
                        'path' => '/path/6/',
                    ],
                ] + self::getBasicRequestData()
            )
        );
    }

    /**
     * @covers Rename::validate()
     * @group svn-server
     * @expectedException \CrowdStar\SVNAgent\Exceptions\ClientException
     * @expectedExceptionMessage given path is empty
     */
    public function testValidateWhenFieldToPathEmpty()
    {
        $request = (new Request())->init(
            [
                'data' => [
                    'path'   => '/path/7/',
                    'toPath' => '',

                ],
            ] + self::getBasicRequestData()
        );

        new Rename($request);
    }

    /**
     * @covers Rename::validate()
     * @group svn-server
     */
    public function testValidateWhenDestinationPathExist()
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage(
            "destination path '" . self::getDummyPathBasedAction('/path/9/')->getSvnDir() . "' already exists"
        );

        $this->mkdir('/path/8/');
        $this->mkdir('/path/9/');

        $request = (new Request())->init(
            [
                'data' => [
                    'path'   => '/path/8/',
                    'toPath' => '/path/9/',
                ],
            ] + self::getBasicRequestData()
        );
        new Rename($request);
    }

    /**
     * @covers Rename::validate()
     * @group svn-server
     * @expectedException \CrowdStar\SVNAgent\Exceptions\ClientException
     * @expectedExceptionMessage source URL 'http://svn-server/svn/project1/path/10/' not exist
     */
    public function testValidateWhenSourceUrlNotExist()
    {
        $this->mkdir('/path/10/');

        $request = (new Request())->init(
            [
                'data' => [
                    'path'   => '/path/10/',
                    'toPath' => '/path/11/',
                ],
            ] + self::getBasicRequestData()
        );
        new Rename($request);
    }

    /**
     * @covers Rename::validate()
     * @group svn-server
     * @expectedException \CrowdStar\SVNAgent\Exceptions\ClientException
     * @expectedExceptionMessage destination URL 'http://svn-server/svn/project1/path/13/' already exists
     */
    public function testValidateWhenDestinationUrlExist()
    {
        foreach (['/path/12/', '/path/13/'] as $path) {
            $request = (new Request())->init(
                [
                    'data' => [
                        'path' => $path,
                    ],
                ] + self::getBasicRequestData()
            );
            $createAction = new Create($request);
            $createAction->run();
        }
        ShellWrap::rm('-rf', $createAction->getSvnDir()); // delete path "/path/13/" only.

        $request = (new Request())->init(
            [
                'data' => [
                    'path'   => '/path/12/',
                    'toPath' => '/path/13/',
                ],
            ] + self::getBasicRequestData()
        );
        new Rename($request);
    }
}
