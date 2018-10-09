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
 * Class RenameResponse
 *
 * @package CrowdStar\SVNAgent\Responses
 */
class RenameResponse extends AbstractVersionedResponse
{
    /**
     * @inheritdoc
     * @see UpdateResponse::process()
     */
    public function process(string $output)
    {
        if (preg_match('/ revision ([\d]+)\.$/', trim($output), $matches)) {
            $this->setRevision(intval($matches[1]));
        } else {
            $this->getLogger()->error("unable to fetch revision # from output when renaming SVN path: {$output}");
            $this->setRevision(-1);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        return [
            'success'  => true,
            'revision' => $this->getRevision(),
        ];
    }
}
