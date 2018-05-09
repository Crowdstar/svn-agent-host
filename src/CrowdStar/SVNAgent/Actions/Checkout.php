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
        $url = $this->getConfig()->getSvnRoot() . $this->getRequest()->get('path');
        $dir = $this->getConfig()->getSvnRootDir() . $this->getRequest()->get('path');
        if (!is_dir($dir)) {
            if (!is_dir(dirname($dir))) {
                mkdir(dirname($dir), 0755, true);
            }

            $this->setMessage('SVN checkout')->exec(
                function () use ($url, $dir) {
                    sh::svn(
                        'checkout',
                        '--username',
                        $this->getRequest()->getUsername(),
                        '--password',
                        $this->getRequest()->getPassword(),
                        $url,
                        $dir
                    );
                }
            );
        } else {
            $this->setError("Folder '{$dir}' already exists");
        }

        return $this;
    }
}
