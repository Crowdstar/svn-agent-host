<?php

namespace CrowdStar\SVNAgent\Traits;

use CrowdStar\SVNAgent\Config;
use Monolog\Logger;

/**
 * Trait LoggerTrait
 *
 * @package CrowdStar\SVNAgent\Traits
 */
trait LoggerTrait
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @return Logger
     */
    public function getLogger(): Logger
    {
        return $this->logger;
    }

    /**
     * @param Logger|null $logger
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
