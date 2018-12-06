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

use CrowdStar\SVNAgent\Exceptions\Exception;
use CrowdStar\SVNAgent\Responses\CheckoutResponse;
use CrowdStar\SVNAgent\Responses\UpdateResponse;
use CrowdStar\SVNAgent\SVNHelper;
use MrRio\ShellWrap;

/**
 * Class Update
 * If given directory not exists, checkout SVN path to under it; otherwise, update SVN repository under that directory.
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class Update extends AbstractPathBasedAction
{
    /**
     * @inheritdoc
     * @todo process responses for SVN checkout only.
     */
    public function processAction(): AbstractAction
    {
        $url = $this->getSvnUri();
        $dir = $this->getSvnDir();

        // Try to create the SVN path first if it doesn't yet exist.
        $create = new Create($this->getRequest(), $this->getLogger());
        $create->processAction();
        if ($create->hasError()) {
            $this->setResponse($create->getResponse());

            return $this;
        }

        if (!is_dir($dir)) {
            if (!is_dir(dirname($dir))) {
                mkdir(dirname($dir), 0755, true);
            }

            $this->checkout($url, $dir);
        } else {
            if (SVNHelper::pathExists($dir)) {
                try {
                    $currentUrl = (new SVNHelper())->getUrl($dir);
                } catch (Exception $e) {
                    $this->setError($e->getMessage());
                }

                if (!$this->hasError()) {
                    if (SVNHelper::sameUrl($currentUrl, $url)) {
                        chdir($dir);
                        $this->setMessage('SVN update')->exec(
                            function () {
                                ShellWrap::svn('update', SVNHelper::getOptions($this->getRequest()));
                            }
                        );
                    } else {
                        $this->setError(
                            "Folder '{$dir}' points to SVN URL $currentUrl which is different from expected URL $url"
                        );
                    }
                }
            } else {
                $this->checkout($url, $dir);
            }
        }

        return $this;
    }

    /**
     * @param string $url
     * @param string $dir
     * @return Update
     * @throws Exception
     */
    protected function checkout(string $url, string $dir): Update
    {
        $this->setResponse(new CheckoutResponse($this->getPath()));
        $this->setMessage('SVN checkout')->exec(
            function () use ($url, $dir) {
                ShellWrap::svn('checkout', $url, $dir, SVNHelper::getOptions($this->getRequest()));
            }
        );

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function initResponse(): AbstractAction
    {
        return $this->setResponse(new UpdateResponse($this->getPath()));
    }
}
