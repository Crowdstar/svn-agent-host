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

namespace CrowdStar\Tests\SVNAgent;

use CrowdStar\SVNAgent\Config;
use CrowdStar\SVNAgent\SVNHelper;
use Exception;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Warning;

/**
 * Class TestListener
 *
 * @package CrowdStar\Tests\SVNAgent
 */
class TestListener implements \PHPUnit\Framework\TestListener
{
    /**
     * @inheritdoc
     */
    public function addError(Test $test, Exception $e, $time)
    {
    }

    /**
     * @inheritdoc
     */
    public function addWarning(Test $test, Warning $e, $time)
    {
    }

    /**
     * @inheritdoc
     */
    public function addFailure(Test $test, AssertionFailedError $e, $time)
    {
    }

    /**
     * @inheritdoc
     */
    public function addIncompleteTest(Test $test, Exception $e, $time)
    {
    }

    /**
     * @inheritdoc
     */
    public function addRiskyTest(Test $test, Exception $e, $time)
    {
    }

    /**
     * @inheritdoc
     */
    public function addSkippedTest(Test $test, Exception $e, $time)
    {
    }

    /**
     * @inheritdoc
     */
    public function startTestSuite(TestSuite $suite)
    {

        if (!empty($_ENV['DEBUG'])) {
            $logFile = Config::singleton()->getLogFile();
            if (!file_exists(dirname($logFile))) {
                mkdir(dirname($logFile), 0755, true);
            }

            file_put_contents(
                Config::singleton()->getLogFile(),
                'SVN version: '. (new SVNHelper())->getSvnVersion() . "\n\n"
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function endTestSuite(TestSuite $suite)
    {
        if (!empty($_ENV['DEBUG'])) {
            printf("PHP logs:\n" . file_get_contents(Config::singleton()->getLogFile()));
        }
    }

    /**
     * @inheritdoc
     */
    public function startTest(Test $test)
    {
    }

    /**
     * @inheritdoc
     */
    public function endTest(Test $test, $time)
    {
    }
}
