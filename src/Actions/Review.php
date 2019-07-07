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

use CrowdStar\SVNAgent\Responses\ReviewResponse;
use MrRio\ShellWrap;

/**
 * Class Review
 * Review local SVN changes under given directory.
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class Review extends AbstractPathBasedAction
{
    /**
     * @inheritdoc
     */
    public function processAction(): AbstractAction
    {
        $dir = $this->getSvnDir();
        if (is_readable($dir)) {
            $this->initializeSvnPathWhenNeeded();

            $this->setMessage('SVN review')->exec(
                function () use ($dir) {
                    ShellWrap::svn('status', $dir);
                }
            );
        } else {
            $this->setError("Folder '{$dir}' not exist");
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function initResponse(): AbstractAction
    {
        return $this->setResponse(new ReviewResponse($this->getPath()));
    }
}
