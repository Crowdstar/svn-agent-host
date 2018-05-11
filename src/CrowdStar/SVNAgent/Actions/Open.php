<?php

namespace CrowdStar\SVNAgent\Actions;

use MrRio\ShellWrap;

/**
 * Class Open
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class Open extends AbstractAction
{
    /**
     * @inheritdoc
     */
    public function processAction(): AbstractAction
    {
        $dir = $this->getSvnDir();
        if (is_dir($dir)) {
            chdir($dir);
            $this->setMessage('open folder in Finder')->exec(
                function () {
                    ShellWrap::open('.');
                }
            );
        } else {
            $this->setError("Folder '{$dir}' not exist");
        }

        return $this;
    }
}
