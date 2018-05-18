<?php

namespace CrowdStar\SVNAgent\Actions;

/**
 * Class Idle
 * To idle for a while without doing anything.
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class Idle extends AbstractAction implements TestActionInterface
{
    /**
     * Maximum number of seconds to idle.
     */
    const MAX_SECONDS = 300;

    /**
     * @var int
     */
    protected $seconds = 15;

    /**
     * @inheritdoc
     */
    public function processAction(): AbstractAction
    {
        $time = time();
        while ((time() - $time) < min($this->getSeconds(), self::MAX_SECONDS)) {
        }
        $this->setMessage('idle')->setResponseMessage("to idle for {$this->getSeconds()} seconds.");

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
    public function setSeconds(int $seconds): Idle
    {
        $this->seconds = $seconds;

        return $this;
    }
}