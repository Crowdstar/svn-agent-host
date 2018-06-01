<?php

namespace CrowdStar\SVNAgent\Traits;

use CrowdStar\SVNAgent\Actions\AbstractAction;
use CrowdStar\SVNAgent\Responses\PathBasedResponse;

/**
 * Trait PathBasedResponseTrait
 *
 * @package CrowdStar\SVNAgent\Traits
 */
trait PathBasedResponseTrait
{
    /**
     * @inheritdoc
     * @see AbstractAction::initResponse()
     */
    protected function initResponse(): AbstractAction
    {
        return $this->setResponse(new PathBasedResponse($this->getPath()));
    }
}
