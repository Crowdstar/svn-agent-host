<?php

namespace CrowdStar\SVNAgent\Traits;

use CrowdStar\SVNAgent\Config;
use Psr\Log\LoggerInterface;

/**
 * Trait LoggerTrait
 *
 * @package CrowdStar\SVNAgent\Traits
 */
trait LoggerTrait
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @param LoggerInterface|null $logger
     * @return $this
     */
    public function setLogger($logger)
    {
        $this->logger = $logger ?: Config::singleton()->getLogger();

        return $this;
    }

    /**
     * Use default logger if not yet setup.
     *
     * @return $this
     */
    protected function initLogger()
    {
        if (!$this->logger) {
            $this->setLogger(null);
        }

        return $this;
    }
}
