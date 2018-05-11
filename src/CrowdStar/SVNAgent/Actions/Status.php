<?php

namespace CrowdStar\SVNAgent\Actions;

use MrRio\ShellWrap;

/**
 * Class Status
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class Status extends AbstractAction
{
    /**
     * @inheritdoc
     */
    public function processAction(): AbstractAction
    {
        $dir = $this->getSvnDir();
        if (is_readable($dir)) {
            $this->setMessage('SVN status')->exec(
                function () use ($dir) {
                    ShellWrap::svn('status', $dir);
                }
            );
        } else {
            $this->setError("Folder '{$dir}' not exist");
        }

        return $this;
    }
}
