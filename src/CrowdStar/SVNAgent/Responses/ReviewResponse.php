<?php

namespace CrowdStar\SVNAgent\Responses;

/**
 * Class ReviewResponse
 *
 * @package CrowdStar\SVNAgent\Responses
 */
class ReviewResponse extends AbstractActionsBasedResponse
{
    /**
     * @inheritdoc
     */
    public function process(string $output)
    {
        array_map(
            function ($line) {
                if (preg_match('/^\s*([^\s]+)\s*([^\s].*)\s*$/', trim($line), $matches)) {
                    $this->addAction(new Action($matches[1], $matches[2]));
                }
            },
            $this->toLines($output)
        );

        return $this;
    }
}
