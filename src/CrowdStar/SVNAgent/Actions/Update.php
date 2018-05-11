<?php

namespace CrowdStar\SVNAgent\Actions;

use CrowdStar\SVNAgent\SVNHelper;
use MrRio\ShellWrap;

/**
 * Class Update
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class Update extends AbstractAction
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
            chdir($dir);

            $sh = new ShellWrap();
            ShellWrap::svn('info', '--show-item', 'url');
            $currentUrl = trim((string) $sh);

            if (SVNHelper::sameUrl($currentUrl, $url)) {
                $this->setMessage('SVN update')->exec(
                    function () use ($dir) {
                        ShellWrap::svn(
                            'update',
                            '--username',
                            $this->getRequest()->getUsername(),
                            '--password',
                            $this->getRequest()->getPassword()
                        );
                    }
                );
            } else {
                $this->setError(
                    "Folder '{$dir}' points to SVN URL $currentUrl which is different from expected URL $url"
                );
            }
        }

        return $this;
    }
}
