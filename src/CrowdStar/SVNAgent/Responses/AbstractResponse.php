<?php

namespace CrowdStar\SVNAgent;

use CrowdStar\SVNAgent\Traits\LoggerTrait;
use Monolog\Logger;

/**
 * Class AbstractResponse
 *
 * @package CrowdStar\SVNAgent
 */
abstract class AbstractResponse
{
    use LoggerTrait;

    /**
     * @var string
     */
    protected $error = '';

    /**
     * Response constructor.
     *
     * @param Logger|null $logger
     */
    public function __construct(Logger $logger = null)
    {
        $this->setLogger($logger);
    }

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
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * @param string $error
     * @return $this
     */
    public function setError(string $error): AbstractResponse
    {
        $this->error = $error;
        $this->getLogger()->error('error: ' . $error);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function hasError(): bool
    {
        return !empty($this->getError());
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * @return array
     */
    abstract public function toArray(): array;
}
