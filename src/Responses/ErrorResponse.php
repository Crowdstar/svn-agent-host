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

use CrowdStar\SVNAgent\Exceptions\Exception;
use CrowdStar\SVNAgent\Traits\LoggerTrait;

/**
 * Class ErrorResponse
 *
 * @package CrowdStar\SVNAgent\Responses
 */
class ErrorResponse extends AbstractResponse
{
    use LoggerTrait;

    /**
     * @var string
     */
    protected $error;

    /**
     * ErrorResponse constructor.
     *
     * @param string $error
     */
    public function __construct(string $error)
    {
        $this->initLogger()->setError($error);
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * @param string $error
     * @return $this
     */
    public function setError(string $error): AbstractResponse
    {
        $this->error = $error;
        $this->getLogger()->error('error: ' . $error);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        return [
            'success' => false,
            'error'   => $this->getError(),
        ];
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function process(string $output)
    {
        throw new Exception(
            'Class ErrorResponse should not need to use method process() to process responses by itself'
        );
    }
}
