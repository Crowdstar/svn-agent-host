<?php

namespace CrowdStar\SVNAgent\Actions;

use MrRio\ShellWrap as sh;

/**
 * Class Checkout
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
        if (is_readable($this->getConfig()->getSvnRootDir())) {
            $this->setMessage('SVN status')->exec(
                function () {
                    sh::svn('status', $this->getConfig()->getSvnRootDir());
                }
            );
        } else {
            $this->setError("Folder \"{$this->getConfig()->getSvnRootDir()}\" not exist");
        }

        return $this;
    }
}
