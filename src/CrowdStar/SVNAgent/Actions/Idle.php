<?php

namespace CrowdStar\SVNAgent\Actions;

/**
 * Class Sleep
 * Sleep for 15 seconds without doing anything.
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class Sleep extends AbstractAction implements TestActionInterface
{
    /**
     * @var int
     */
    protected $seconds = 15;

    /**
     * @inheritdoc
     */
    public function processAction(): AbstractAction
    {
        sleep($this->getSeconds());
        $this->setMessage('sleep')->setResponseMessage("slept for {$this->getSeconds()} seconds.");

        return $this;
    }

    /**
     * @return int
     */
    public function getSeconds(): int
    {
        return $this->seconds;
    }

    /**
     * @param int $seconds
     * @return $this
     */
    public function setSeconds(int $seconds): Sleep
    {
        $this->seconds = $seconds;

        return $this;
    }
}
