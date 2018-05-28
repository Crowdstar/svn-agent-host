<?php

namespace CrowdStar\SVNAgent\Actions;

/**
 * Class Version
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class Version extends AbstractAction implements LocklessActionInterface, PathNotRequiredActionInterface
{
    /**
     * @inheritdoc
     */
    public function processAction(): AbstractAction
    {
        $this->setMessage('version')->setResponseMessage($this->getConfig()::VERSION);

        return $this;
    }
}
