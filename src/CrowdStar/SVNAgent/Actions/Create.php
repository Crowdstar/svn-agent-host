<?php

namespace CrowdStar\SVNAgent\Actions;

use CrowdStar\SVNAgent\SVNHelper;
use MrRio\ShellWrap;

/**
 * Class Create
 * If given directory not exists, create it in SVN.
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class Create extends AbstractAction
{
    /**
     * @inheritdoc
     */
    public function processAction(): AbstractAction
    {
        $url = $this->getSvnUri();
        $dir = $this->getSvnDir();
        if (!SVNHelper::urlExists($url, $this->getRequest())) {
            $this->setMessage('SVN mkdir')->exec(
                function () use ($url, $dir) {
                    ShellWrap::svn(
                        'mkdir',
                        $url,
                        '--parents',
                        [
                            'username' => $this->getRequest()->getUsername(),
                            'password' => $this->getRequest()->getPassword(),
                            'm'        => 'path added through SVN Agent',
                        ]
                    );
                },
                function () use ($url, $dir) {
                    if (!is_dir(dirname($dir))) {
                        mkdir(dirname($dir), 0755, true);
                    }

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
        }

        if (!$this->hasError()) {
            $this->setResponseMessage("SVN path '{$url}' is created and has been checked out locally.");
        }

        return $this;
    }
}
