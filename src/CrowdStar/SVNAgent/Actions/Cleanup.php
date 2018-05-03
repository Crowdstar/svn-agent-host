<?php

namespace CrowdStar\SVNAgent\Actions;

use MrRio\ShellWrap as sh;

/**
 * Class Checkout
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
        if (is_readable($this->getConfig()->getSvnRootDir())) {
            chdir($this->getConfig()->getSvnRootDir());
            $this->setMessage('SVN cleanup')->exec(
                function () {
                    sh::bash($this->getConfig()->getRootDir() . '/vendor/bin/svn-cleanup.sh');
                }
            );
        } else {
            $this->setError("Folder \"{$this->getConfig()->getSvnRootDir()}\" not exist");
        }

        return $this;
    }
}
