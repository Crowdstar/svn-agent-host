<?php

namespace CrowdStar\SVNAgent\Actions;

use MrRio\ShellWrap;

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
        $url = $this->getSvnUri();
        $dir = $this->getSvnDir();
        if (!is_dir($dir)) {
            if (!is_dir(dirname($dir))) {
                mkdir(dirname($dir), 0755, true);
            }

            $this->setMessage('SVN checkout')->exec(
                function () use ($url, $dir) {
                    ShellWrap::svn(
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
