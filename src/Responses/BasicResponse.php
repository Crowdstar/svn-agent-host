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

/**
 * Class BasicResponse
 *
 * @package CrowdStar\SVNAgent\Responses
 */
class BasicResponse extends AbstractResponse
{
    /**
     * @var string
     */
    protected $message = '';

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message): BasicResponse
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        return [
            'success' => true,
            'message' => $this->getMessage(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function process(string $output)
    {
        $this->setMessage($output);

        return $this;
    }
}
