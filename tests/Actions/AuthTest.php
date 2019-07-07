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
use CrowdStar\SVNAgent\Actions\Auth;
use CrowdStar\SVNAgent\Exceptions\ClientException;
use CrowdStar\SVNAgent\Request;
use CrowdStar\Tests\SVNAgent\AbstractSvnTestCase;

/**
 * Class AuthTest
 *
 * @package CrowdStar\Tests\SVNAgent\Actions
 */
class AuthTest extends AbstractSvnTestCase
{
    /**
     * @return array
     */
    public function dataProcessAction(): array
    {
        return [
            [
                false,
                array_merge(self::getBasicRequestData(), ['username' => 'invalid_username']),
                'SVN access with invalid username',
            ],
            [
                true,
                self::getBasicRequestData(),
                'SVN access with valid username/password',
            ],
            [
                false,
                array_merge(self::getBasicRequestData(), ['password' => 'invalid_password']),
                'SVN access with invalid password',
            ],
        ];
    }

    /**
     * @dataProvider dataProcessAction
     * @covers AbstractAction::run()
     * @covers AbstractAction::process()
     * @covers Auth::processAction()
     * @group svn-server
     * @param bool $expected
     * @param array $requestData
     * @param string $message
     * @throws ClientException
     */
    public function testProcessAction(bool $expected, array $requestData, string $message)
    {
        $this->assertSame(
            $expected,
            (new Auth((new Request())->init($requestData)))->run()->toArray()['success'],
            $message
        );
    }
}
