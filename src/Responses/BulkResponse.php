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

/**
 * Class BulkResponse
 *
 * @package CrowdStar\SVNAgent\Responses
 */
class BulkResponse extends AbstractResponse
{
    /**
     * @var AbstractResponse[]
     */
    protected $responses = [];

    /**
     * @return AbstractResponse[]
     */
    public function getResponses(): array
    {
        return $this->responses;
    }

    /**
     * @param AbstractResponse[] $responses
     * @return $this
     */
    public function setResponses(array $responses): BulkResponse
    {
        $this->responses = $responses;

        return $this;
    }

    /**
     * @param AbstractResponse $response
     * @return $this
     */
    public function addResponse(AbstractResponse $response): BulkResponse
    {
        $this->responses[] = $response;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        return [
            'success'  => true,
            'response' => array_map(
                function (AbstractResponse $response) {
                    return $response->toArray();
                },
                $this->getResponses()
            ),
        ];
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function process(string $output)
    {
        throw new Exception('Bulk actions should not need to use method process() to process responses by itself');
    }
}
