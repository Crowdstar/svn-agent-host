<?php

namespace CrowdStar\SVNAgent\Actions;

use CrowdStar\SVNAgent\Config;
use MrRio\ShellWrap;
use ReflectionClass;

/**
 * Class Unlock
 * Kill running processes of this messaging host and delete lock file so that new actions from the extension can be
 * fired.
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class Unlock extends AbstractAction implements LocklessActionInterface, PathNotRequiredActionInterface
{
    /**
     * @inheritdoc
     */
    public function processAction(): AbstractAction
    {
        $this->setMessage('unlock')->exec(
            function () {
                $this->getLogger()->info('current pid is: ' . getmypid());
                (new ShellWrap())(
                    $this->getConfig()->getRootDir() . '/vendor/bin/kill-host-processes.sh',
                    getmypid(),
                    $this->getConfig()->getExtensionId(),
                    $this->getLockFilePath()
                );
            }
        );

        return $this;
    }

    /**
     * @return string
     * @throws \ReflectionException
     * @see \NinjaMutex\Lock\FlockLock::getFilePath
     */
    protected function getLockFilePath(): string
    {
        $mutex    = $this->getMutex();
        $class    = new ReflectionClass($mutex);
        $property = $class->getProperty('lockImplementor');
        $property->setAccessible(true);
        $lock     = $property->getValue($mutex);

        $class  = new ReflectionClass($lock);
        $method = $class->getMethod('getFilePath');
        $method->setAccessible(true);

        return $method->invokeArgs($lock, [getenv(Config::SVN_AGENT_MUTEX_NAME)]);
    }
}
