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
use CrowdStar\SVNAgent\WindowsCompatibleInterface;

/**
 * Class Open
 * Open the parent directory of given directory in Finder (Mac OS) or File Explorer (Windows).
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class Open extends AbstractPathBasedAction implements WindowsCompatibleInterface
{
    use SimpleResponseTrait;

    /**
     * @inheritdoc
     */
    public function processAction(): AbstractAction
    {
        // PHP code "ShellWrap::open($dir);" won't work if path $dir contains space. Because of that, we have to switch
        // to given directory first, then open it.
        $dir = dirname($this->getSvnDir());
        if (is_dir($dir)) {
            chdir($dir);
            $this->setMessage('open folder')->exec(
                function () {
                    if ($this->getConfig()->onWindows()) {
                        exec('explorer .'); // open folder in File Explorer on Windows
                    } else {
                        exec('open .'); // open folder in Finder on Mac
                    }
                }
            );
        } else {
            $this->setError("Folder '{$dir}' not exist");
        }

        return $this;
    }
}
