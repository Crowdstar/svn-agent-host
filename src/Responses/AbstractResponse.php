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

namespace CrowdStar\SVNAgent\Responses;

use CrowdStar\SVNAgent\Config;
use Psr\Log\LoggerInterface;

/**
 * Class AbstractResponse
 *
 * @package CrowdStar\SVNAgent\Responses
 */
abstract class AbstractResponse
{
    /**
     * @return $this
     */
    public function sendResponse(): AbstractResponse
    {
        $stdout   = fopen('php://stdout', 'w');
        $response = (string) $this;
        fwrite($stdout, pack('I', strlen($response)) . $response);
        fclose($stdout);

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * @return LoggerInterface
     */
    protected function getLogger(): LoggerInterface
    {
        return Config::singleton()->getLogger();
    }

    /**
     * @return array
     */
    abstract public function toArray(): array;

    /**
     * Process console output.
     *
     * @param string $output
     * @return $this
     */
    abstract public function process(string $output);
}
