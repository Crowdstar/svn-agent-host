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

namespace CrowdStar\SVNAgent\Traits;

use CrowdStar\SVNAgent\Config;
use Psr\Log\LoggerInterface;

/**
 * Trait LoggerTrait
 *
 * @package CrowdStar\SVNAgent\Traits
 */
trait LoggerTrait
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @param LoggerInterface|null $logger
     * @return $this
     */
    public function setLogger($logger)
    {
        $this->logger = $logger ?: Config::singleton()->getLogger();

        return $this;
    }

    /**
     * Use default logger if not yet setup.
     *
     * @return $this
     */
    protected function initLogger()
    {
        if (!$this->logger) {
            $this->setLogger(null);
        }

        return $this;
    }
}
