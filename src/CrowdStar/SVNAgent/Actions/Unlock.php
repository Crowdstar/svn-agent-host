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

namespace CrowdStar\SVNAgent\Actions;

use CrowdStar\SVNAgent\Config;
use CrowdStar\SVNAgent\Traits\SimpleResponseTrait;
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
    use SimpleResponseTrait;

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
