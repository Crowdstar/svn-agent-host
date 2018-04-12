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
     * @var string
     */
    protected $loggerName = '';

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @return string
     */
    public function getLoggerName(): string
    {
        return $this->loggerName;
    }

    /**
     * @param string $loggerName
     * @return $this
     */
    public function setLoggerName(string $loggerName)
    {
        $this->loggerName = $loggerName;

        return $this;
    }

    /**
     * @return Logger
     */
    public function getLogger(): Logger
    {
        return $this->logger;
    }

    /**
     * @param Logger|null $logger
     * @param string $loggerName
     * @return $this
     */
    public function setLogger($logger, string $loggerName = null)
    {
        $logger       = $logger ?: Config::singleton()->getLogger();
        $loggerName   = $loggerName ?? $this->getLoggerName();
        $this->logger = ($loggerName ? $logger->withName($loggerName) : $logger);

        return $this;
    }
}
