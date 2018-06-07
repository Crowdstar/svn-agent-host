<?php

namespace CrowdStar\SVNAgent\Responses;

/**
 * Class AbstractVersionedResponse
 *
 * @package CrowdStar\SVNAgent\Responses
 */
abstract class AbstractVersionedResponse extends AbstractActionsBasedResponse
{
    /**
     * @var int
     */
    protected $revision;

    /**
     * @return int
     */
    public function getRevision(): int
    {
        return $this->revision;
    }

    /**
     * @param int $revision
     * @return AbstractVersionedResponse
     */
    public function setRevision(int $revision): AbstractVersionedResponse
    {
        $this->revision = $revision;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        return parent::toArray() + ['revision' => $this->getRevision()];
    }
}
