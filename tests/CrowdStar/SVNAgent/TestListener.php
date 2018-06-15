<?php

namespace CrowdStar\Tests\SVNAgent;

use CrowdStar\SVNAgent\Config;
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
            file_put_contents(Config::singleton()->getLogFile(), '');
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
