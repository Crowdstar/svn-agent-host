<?php

namespace CrowdStar\SVNAgent\Actions;

use CrowdStar\SVNAgent\Traits\SimpleResponseTrait;
use MrRio\ShellWrap;

/**
 * Class Open
 * Open given directory in Finder.
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class Open extends AbstractAction implements PathBasedActionInterface
{
    use SimpleResponseTrait;

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
