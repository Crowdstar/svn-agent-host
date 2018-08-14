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

use CrowdStar\SVNAgent\Traits\SimpleResponseTrait;

/**
 * Class Idle
 * To idle for a while without doing anything.
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class Idle extends AbstractAction implements PathNotRequiredActionInterface, TestActionInterface
{
    use SimpleResponseTrait;

    /**
     * Maximum number of seconds to idle.
     */
    const MAX_SECONDS = 300;

    /**
     * @var int
     */
    protected $seconds = 15;

    /**
     * @inheritdoc
     */
    public function processAction(): AbstractAction
    {
        $time    = time();
        $seconds = min($this->getSeconds(), self::MAX_SECONDS);
        while ((time() - $time) < $seconds) {
        }
        $this->setMessage('idle')->prepareResponse("to idle for {$seconds} seconds.");

        return $this;
    }

    /**
     * @return int
     */
    public function getSeconds(): int
    {
        return $this->seconds;
    }

    /**
     * @param int $seconds
     * @return $this
     */
    public function setSeconds(int $seconds): Idle
    {
        $this->seconds = $seconds;

        return $this;
    }
}
