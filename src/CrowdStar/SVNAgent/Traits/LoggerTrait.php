<?php

namespace CrowdStar\SVNAgent\Traits;

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
     * @param Logger $logger
     * @param string $loggerName
     * @return $this
     */
    public function setLogger(Logger $logger, string $loggerName = null)
    {
        $loggerName   = $loggerName ?? $this->getLoggerName();
        $this->logger = ($loggerName ? $logger->withName($loggerName) : $logger);

        return $this;
    }
}
