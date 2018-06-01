<?php

namespace CrowdStar\SVNAgent\Actions;

use CrowdStar\SVNAgent\Responses\ReviewResponse;
use MrRio\ShellWrap;

/**
 * Class Review
 * Review local SVN changes under given directory.
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class Review extends AbstractAction
{
    /**
     * @inheritdoc
     */
    public function processAction(): AbstractAction
    {
        $dir = $this->getSvnDir();
        if (is_readable($dir)) {
            $this->initializeSvnPathWhenNeeded();

            $this->setMessage('SVN review')->exec(
                function () use ($dir) {
                    ShellWrap::svn('status', $dir);
                }
            );
        } else {
            $this->setError("Folder '{$dir}' not exist");
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function initResponse(): AbstractAction
    {
        return $this->setResponse(new ReviewResponse($this->getPath()));
    }
}
