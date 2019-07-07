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

use CrowdStar\SVNAgent\Traits\ResponseActionsTrait;

/**
 * Class AbstractActionsBasedResponse
 *
 * @package CrowdStar\SVNAgent\Responses
 */
abstract class AbstractActionsBasedResponse extends AbstractPathBasedResponse
{
    use ResponseActionsTrait;

    /**
     * @inheritdoc
     */
    public function __construct(string $path)
    {
        parent::__construct($path);
        $this->setActions(new Actions());
    }

    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        return parent::toArray() + ['actions' => $this->getActions()->toArray()];
    }

    /**
     * @param string $output
     * @return array
     */
    protected function toLines(string $output): array
    {
        return array_filter(array_map('trim', explode("\n", $output)));
    }
}
