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
 * Class Actions
 *
 * @package CrowdStar\SVNAgent\Responses
 */
class Actions
{
    /**
     * @var Action[]
     */
    protected $actions;

    /**
     * Actions constructor.
     *
     * @param array $actions
     */
    public function __construct(array $actions = [])
    {
        $this->setActions($actions);
    }

    /**
     * @return Action[]
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * @param Action[] $actions
     * @return $this
     */
    public function setActions(array $actions): Actions
    {
        $this->actions = $actions;

        return $this;
    }

    /**
     * @param Action $action
     * @return Action
     */
    public function addAction(Action $action): Actions
    {
        $this->actions[] = $action;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_map(
            function (Action $action) {
                return $action->toArray();
            },
            $this->getActions()
        );
    }
}
