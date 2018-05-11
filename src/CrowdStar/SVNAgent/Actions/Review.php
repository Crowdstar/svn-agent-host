<?php

namespace CrowdStar\SVNAgent\Actions;

use MrRio\ShellWrap;

/**
 * Class Review
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class Review extends AbstractAction
{
    /**
     * @inheritdoc
     */
    public function processAction(): AbstractAction
    {
        $dir = $this->getSvnDir();
        if (is_readable($dir)) {
            $this->setMessage('SVN review')->exec(
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
