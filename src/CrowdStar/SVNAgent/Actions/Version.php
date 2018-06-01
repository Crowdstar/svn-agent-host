<?php

namespace CrowdStar\SVNAgent\Actions;

use CrowdStar\SVNAgent\Traits\SimpleResponseTrait;

/**
 * Class Version
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class Version extends AbstractAction implements LocklessActionInterface, PathNotRequiredActionInterface
{
    use SimpleResponseTrait;

    /**
     * @inheritdoc
     */
    public function processAction(): AbstractAction
    {
        $this->setMessage('version')->prepareResponse($this->getConfig()::VERSION);

        return $this;
    }
}
