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
use MrRio\ShellWrap;
use MrRio\ShellWrapException;

/**
 * Class Commit
 * Commit all local changes under given directory to SVN.
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class Commit extends AbstractAction implements PathBasedActionInterface
{
    use PathBasedResponseTrait;

    /**
     * @inheritdoc
     * @todo use a single script file to run all commands.
     */
    public function processAction(): AbstractAction
    {
        $dir = $this->getSvnDir();
        if (is_readable($dir)) {
            chdir($dir);

            $this->initializeSvnPathWhenNeeded();

            // @see https://stackoverflow.com/a/11066348/2752269 svn delete removed files
            if (trim(ShellWrap::svn('status | grep \'^!\' | awk \'{print $2}\''))) {
                ShellWrap::svn('status | grep \'^!\' | awk \'{print $2}\' | xargs svn delete');
            }
            try {
                // @see https://stackoverflow.com/a/4046862/2752269 How do I 'svn add' all unversioned files to SVN?
                ShellWrap::svn(
                    'add --force * --auto-props --parents --depth infinity -q'
                );
            } catch (ShellWrapException $e) {
                // This "svn add" command fails if nothing to add.
            }

            $this->setMessage('SVN commit')->exec(
                function () use ($dir) {
                    ShellWrap::svn(
                        'commit',
                        [
                            'username' => $this->getRequest()->getUsername(),
                            'password' => $this->getRequest()->getPassword(),
                            'm'        => 'changes committed through SVN Agent',
                        ]
                    );

                    // Delete the local folder after committed.
                    // TODO: backup it first before deleting it.
                    chdir('..');
                    ShellWrap::rm('-rf', $dir);
                }
            );

            //ShellWrap::svn(
            //    'up',
            //    [
            //        'username' => $this->getRequest()->getUsername(),
            //        'password' => $this->getRequest()->getPassword(),
            //    ]
            //);
        } else {
            $this->setError("Folder '{$dir}' not exist");
        }

        return $this;
    }
}
