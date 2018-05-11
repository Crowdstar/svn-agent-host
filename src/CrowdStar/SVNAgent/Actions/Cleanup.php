<?php

namespace CrowdStar\SVNAgent\Actions;

use MrRio\ShellWrap;

/**
 * Class Cleanup
 * Clean up and discard local SVN changes under given directory.
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class Cleanup extends AbstractAction
{
    /**
     * @inheritdoc
     */
    public function processAction(): AbstractAction
    {
        $dir = $this->getSvnDir();
        if (is_readable($dir)) {
            chdir($dir);
            $this->setMessage('SVN cleanup')->exec(
                function () {
                    ShellWrap::bash($this->getConfig()->getRootDir() . '/vendor/bin/svn-cleanup.sh');
                }
            );
        } else {
            $this->setError("Folder '{$dir}' not exist");
        }

        return $this;
    }
}
