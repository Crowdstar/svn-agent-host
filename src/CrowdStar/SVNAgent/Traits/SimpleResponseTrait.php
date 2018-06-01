<?php

namespace CrowdStar\SVNAgent\Traits;

use CrowdStar\SVNAgent\Actions\AbstractAction;
use CrowdStar\SVNAgent\Responses\Response;

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
        return $this->setResponse(new Response());
    }
}
