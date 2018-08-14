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

use CrowdStar\SVNAgent\Traits\SimpleResponseTrait;
use MrRio\ShellWrap;

/**
 * Class UpdateCredentials
 *
 * @package CrowdStar\SVNAgent\Actions
 * @todo This doesn't work on Mac OS X since SVN credentials are stored in keychain and here we use credentials not
 *       from a terminal.
 */
class UpdateCredentials extends AbstractAction implements PathNotRequiredActionInterface
{
    use SimpleResponseTrait;

    /**
     * @inheritdoc
     */
    public function processAction(): AbstractAction
    {
        $this->setMessage('SVN save credentials')->exec(
            function () {
                ShellWrap::bash($this->getConfig()->getRootDir() . '/vendor/bin/svn-save-credentials.sh');
                //ShellWrap::svn(
                //    'auth',
                //    '--remove',
                //    $this->getConfig()->getSvnRoot() //TODO: change this to base URL like http://example.com
                //);
                ShellWrap::svn(
                    'info',
                    $this->getConfig()->getSvnRoot(),
                    [
                        'username' => $this->getRequest()->getUsername(),
                        'password' => $this->getRequest()->getPassword(),
                    ]
                );
            }
        );

        return $this;
    }
}
