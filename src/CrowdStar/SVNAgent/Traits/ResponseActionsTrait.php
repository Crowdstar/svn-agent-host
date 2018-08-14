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

use CrowdStar\SVNAgent\Responses\Action;
use CrowdStar\SVNAgent\Responses\Actions;

/**
 * Trait ResponseActionsTrait
 *
 * @package CrowdStar\SVNAgent\Traits
 */
trait ResponseActionsTrait
{
    /**
     * @var Actions
     */
    protected $actions;

    /**
     * @return Actions
     */
    public function getActions(): Actions
    {
        return $this->actions;
    }

    /**
     * @param Actions $actions
     * @return $this
     */
    public function setActions(Actions $actions)
    {
        $this->actions = $actions;

        return $this;
    }

    /**
     * @param Action $action
     * @return $this
     */
    protected function addAction(Action $action)
    {
        $this->getActions()->addAction($action);

        return $this;
    }
}
