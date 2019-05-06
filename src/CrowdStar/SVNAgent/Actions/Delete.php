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

use CrowdStar\SVNAgent\SVNHelper;
use CrowdStar\SVNAgent\Traits\SimpleResponseTrait;
use MrRio\ShellWrap;

/**
 * Class Delete
 * Delete given directory from remote SVN repository and from local file disk.
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class Delete extends AbstractPathBasedAction
{
    use SimpleResponseTrait;

    /**
     * @inheritdoc
     */
    public function processAction(): AbstractAction
    {
        $dir = $this->getSvnDir();
        if (is_dir($dir)) {
            rename($dir, ($this->initBackupDir() . DIRECTORY_SEPARATOR . uniqid(date('YmdHis-'))));
            $this->prepareResponse('local folder deleted');
        }

        $url = $this->getSvnUri();
        if (SVNHelper::urlExists($url, $this->getRequest())) {
            $this->setMessage('SVN folder deletion')->exec(
                function () use ($url) {
                    ShellWrap::svn(
                        'delete',
                        $url,
                        SVNHelper::getOptions($this->getRequest(), ['m' => "delete path {$url}"])
                    );
                }
            );
        }

        return $this;
    }
}
