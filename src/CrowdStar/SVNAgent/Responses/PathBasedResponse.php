<?php

namespace CrowdStar\SVNAgent\Responses;

/**
 * Class PathBasedResponse
 *
 * @package CrowdStar\SVNAgent\Responses
 */
class PathBasedResponse extends AbstractPathBasedResponse
{
    /**
     * @inheritdoc
     */
    public function process(string $output)
    {
        // Path-based response doesn't not need to process outputs by itself.
        return $this;
    }
}
