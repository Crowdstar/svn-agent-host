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

use CrowdStar\SVNAgent\Responses\ExportResponse;
use CrowdStar\SVNAgent\SVNHelper;
use MrRio\ShellWrap;

/**
 * Class Export
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class Export extends AbstractPathBasedAction
{
    /**
     * @inheritdoc
     */
    public function processAction(): AbstractAction
    {
        $url = $this->getSvnUri();
        $dir = $this->getSvnDir();

        if (!is_dir($dir)) {
            if (SVNHelper::urlExists($url, $this->getRequest())) {
                $this->setMessage('SVN export')->exec(
                    function () use ($url, $dir) {
                        ShellWrap::svn(
                            'export',
                            $url,
                            $dir,
                            SVNHelper::getOptions($this->getRequest())
                        );
                    }
                );
            } else {
                $this->setError("URL {$url} not exists.");
            }
        } else {
            $this->setError("Folder '{$dir}' already exists. Please delete that folder first before exporting.");
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function initResponse(): AbstractAction
    {
        return $this->setResponse(new ExportResponse($this->getPath()));
    }
}
