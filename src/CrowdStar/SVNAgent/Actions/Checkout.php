<?php

namespace CrowdStar\SVNAgent\Actions;

use MrRio\ShellWrap as sh;

/**
 * Class Checkout
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class Checkout extends AbstractAction
{
    /**
     * @inheritdoc
     */
    public function processAction(): AbstractAction
    {
        if (!is_readable($this->getConfig()->getSvnRootDir())) {
            $this->setMessage('SVN checkout')->exec(
                function () {
                    sh::svn(
                        'checkout',
                        '--username',
                        $this->getRequest()->getUsername(),
                        '--password',
                        $this->getRequest()->getPassword(),
                        $this->getConfig()->getSvnTrunk(),
                        $this->getConfig()->getSvnRootDir()
                    );
                }
            );
        } else {
            $this->setError("Folder \"{$this->getConfig()->getSvnRootDir()}\" already exists");
        }

        return $this;
    }
}
