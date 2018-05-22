<?php

namespace CrowdStar\SVNAgent\Actions;

use CrowdStar\SVNAgent\Exceptions\Exception;
use CrowdStar\SVNAgent\SVNHelper;
use MrRio\ShellWrap;

/**
 * Class Update
 * If given directory not exists, checkout SVN path to under it; otherwise, update SVN repository under that directory.
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
            try {
                $currentUrl = SVNHelper::getUrl($dir);
            } catch (Exception $e) {
                $this->setError($e->getMessage());
            }

            if (!$this->hasError()) {
                if (SVNHelper::sameUrl($currentUrl, $url)) {
                    chdir($dir);
                    $this->setMessage('SVN update')->exec(
                        function () {
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
        }

        return $this;
    }
}
