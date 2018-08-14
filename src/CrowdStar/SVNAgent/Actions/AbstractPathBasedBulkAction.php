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

use CrowdStar\SVNAgent\Responses\BulkResponse;

/**
 * Class AbstractPathBasedBulkAction
 *
 * @package CrowdStar\SVNAgent\Actions
 */
abstract class AbstractPathBasedBulkAction extends AbstractBulkAction
{
    /**
     * @inheritdoc
     */
    public function processAction(): AbstractAction
    {
        $request = clone $this->getRequest();
        $request->setAction($this->getBasicAction());
        /** @var BulkResponse $bulkResponse */
        $bulkResponse = $this->getResponse();

        foreach ($this->getPaths() as $path) {
            $request->setData(['path' => $path]);
            $bulkResponse->addResponse(ActionFactory::fromRequest($request, $this->getLogger())->run());
        }

        return $this;
    }
}
