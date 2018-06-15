<?php

namespace CrowdStar\SVNAgent\Responses;

use CrowdStar\SVNAgent\Config;
use Psr\Log\LoggerInterface;

/**
 * Class AbstractResponse
 *
 * @package CrowdStar\SVNAgent\Responses
 */
abstract class AbstractResponse
{
    /**
     * @return $this
     */
    public function sendResponse(): AbstractResponse
    {
        $stdout   = fopen('php://stdout', 'w');
        $response = (string) $this;
        fwrite($stdout, pack('I', strlen($response)) . $response);
        fclose($stdout);

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * @return LoggerInterface
     */
    protected function getLogger(): LoggerInterface
    {
        return Config::singleton()->getLogger();
    }

    /**
     * @return array
     */
    abstract public function toArray(): array;

    /**
     * Process console output.
     *
     * @param string $output
     * @return $this
     */
    abstract public function process(string $output);
}
