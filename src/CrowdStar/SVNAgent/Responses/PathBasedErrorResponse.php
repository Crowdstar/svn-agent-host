<?php

namespace CrowdStar\SVNAgent\Responses;

use CrowdStar\SVNAgent\Traits\PathTrait;

/**
 * Class PathBasedErrorResponse
 *
 * @package CrowdStar\SVNAgent\Responses
 */
class PathBasedErrorResponse extends ErrorResponse
{
    use PathTrait;

    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        return ['path' => $this->getPath()] + parent::toArray();
    }
}
