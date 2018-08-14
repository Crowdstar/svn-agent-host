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

use CrowdStar\SVNAgent\Traits\PathBasedResponseTrait;
use CrowdStar\SVNAgent\SVNHelper;
use MrRio\ShellWrap;

/**
 * Class Create
 * If given directory not exists, create it in SVN.
 *
 * @package CrowdStar\SVNAgent\Actions
 * @deprecated Please use action \CrowdStar\SVNAgent\Actions\Update instead.
 */
class Create extends AbstractAction implements PathBasedActionInterface
{
    use PathBasedResponseTrait;

    /**
     * @inheritdoc
     */
    public function processAction(): AbstractAction
    {
        $url = $this->getSvnUri();
        if (!SVNHelper::urlExists($url, $this->getRequest())) {
            $this->setMessage('SVN mkdir')->exec(
                function () use ($url) {
                    ShellWrap::svn(
                        'mkdir',
                        $url,
                        '--parents',
                        [
                            'username' => $this->getRequest()->getUsername(),
                            'password' => $this->getRequest()->getPassword(),
                            'm'        => 'path added through SVN Agent',
                        ]
                    );
                }
            );
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function getPostActions(): array
    {
        return [new Update($this->getRequest(), $this->getLogger())];
    }
}
