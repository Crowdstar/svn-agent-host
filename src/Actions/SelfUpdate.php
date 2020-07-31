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

use CrowdStar\SVNAgent\Config;
use CrowdStar\SVNAgent\Exceptions\Exception;
use CrowdStar\SVNAgent\Traits\SimpleResponseTrait;
use MrRio\ShellWrap;

/**
 * Class SelfUpdate
 * To upgrade the SVN Agent Host package and other PHP packages through Composer.
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class SelfUpdate extends AbstractAction implements PathNotRequiredActionInterface
{
    use SimpleResponseTrait;

    /**
     * @inheritdoc
     */
    public function processAction(): AbstractAction
    {
        try {
            $composer   = $this->getComposer();
            $workingDir = $this->getComposerWorkingDir();
        } catch (Exception $e) {
            $this->setError($e->getMessage());
        }

        if (!empty($composer) && !empty($workingDir)) {
            $this->getLogger()->info('start upgrading SVN Agent Host ' . Config::VERSION);
            $this->setMessage('self-update')->exec(
                function () use ($composer, $workingDir) {
                    (new ShellWrap())($composer, 'update', '-d', $workingDir, '--no-dev', '-n');
                }
            );
        }

        return $this;
    }

    protected function getComposer(): string
    {
        foreach ($this->getDirs() as $dir) {
            // To wrap the official Composer executable, you can create a "composer.sh" file for that.
            foreach (['composer.sh', 'composer', 'composer.phar'] as $file) {
                $composer = $dir . DIRECTORY_SEPARATOR . $file;
                if (is_executable($composer)) {
                    return $composer;
                }
            }
        }

        throw new Exception('Unable to locate the Composer executable.');
    }

    protected function getComposerWorkingDir(): string
    {
        foreach ($this->getDirs() as $dir) {
            $file = $dir . '/vendor/autoload.php';
            if (file_exists($file)) {
                return $dir;
            }
        }

        throw new Exception('Unable to locate Composer working directory.');
    }

    protected function getDirs(): array
    {
        return [
            dirname(__DIR__, 2), // Assuming this file sits under folder src/Actions/.
            dirname(__DIR__, 5), // Assuming this file sits under folder vendor/crowdstar/svn-agent-host/src/Actions/.
        ];
    }
}
