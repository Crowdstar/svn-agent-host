<?php

namespace CrowdStar\SVNAgent\Actions;

use CrowdStar\SVNAgent\Exceptions\Exception;
use CrowdStar\SVNAgent\Responses\CheckoutResponse;
use CrowdStar\SVNAgent\Responses\UpdateResponse;
use CrowdStar\SVNAgent\SVNHelper;
use MrRio\ShellWrap;

/**
 * Class Update
 * If given directory not exists, checkout SVN path to under it; otherwise, update SVN repository under that directory.
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class Update extends AbstractAction implements PathBasedActionInterface
{
    /**
     * @inheritdoc
     * @todo process responses for SVN checkout only.
     */
    public function processAction(): AbstractAction
    {
        $url = $this->getSvnUri();
        $dir = $this->getSvnDir();

        // Try to create the SVN path first if it doesn't yet exist.
        $create = new Create($this->getRequest(), $this->getLogger());
        $create->processAction();
        if ($create->hasError()) {
            $this->setResponse($create->getResponse());

            return $this;
        }

        if (!is_dir($dir)) {
            if (!is_dir(dirname($dir))) {
                mkdir(dirname($dir), 0755, true);
            }

            $this->checkout($url, $dir);
        } else {
            if (SVNHelper::pathExists($dir)) {
                try {
                    $currentUrl = (new SVNHelper())->getUrl($dir);
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
            } else {
                $this->checkout($url, $dir);
            }
        }

        return $this;
    }

    /**
     * @param string $url
     * @param string $dir
     * @return Update
     * @throws Exception
     */
    protected function checkout(string $url, string $dir): Update
    {
        $this->setResponse(new CheckoutResponse($this->getPath()));
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

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function initResponse(): AbstractAction
    {
        return $this->setResponse(new UpdateResponse($this->getPath()));
    }
}
