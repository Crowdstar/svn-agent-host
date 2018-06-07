<?php

namespace CrowdStar\SVNAgent\Responses;

use CrowdStar\SVNAgent\Traits\ResponseActionsTrait;

/**
 * Class AbstractActionsBasedResponse
 *
 * @package CrowdStar\SVNAgent\Responses
 */
abstract class AbstractActionsBasedResponse extends AbstractPathBasedResponse
{
    use ResponseActionsTrait;

    /**
     * @inheritdoc
     */
    public function __construct(string $path)
    {
        parent::__construct($path);
        $this->setActions(new Actions());
    }

    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        return parent::toArray() + ['actions' => $this->getActions()->toArray()];
    }

    /**
     * @param string $output
     * @return array
     */
    protected function toLines(string $output): array
    {
        return array_filter(array_map('trim', explode("\n", $output)));
    }
}
