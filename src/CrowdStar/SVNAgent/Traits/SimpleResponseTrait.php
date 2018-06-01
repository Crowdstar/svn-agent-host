<?php

namespace CrowdStar\SVNAgent\Traits;

use CrowdStar\SVNAgent\Actions\AbstractAction;
use CrowdStar\SVNAgent\Responses\BasicResponse;

/**
 * Trait SimpleResponseTrait
 *
 * @package CrowdStar\SVNAgent\Traits
 */
trait SimpleResponseTrait
{
    /**
     * @inheritdoc
     * @see AbstractAction::initResponse()
     */
    protected function initResponse(): AbstractAction
    {
        return $this->setResponse(new BasicResponse());
    }
}
