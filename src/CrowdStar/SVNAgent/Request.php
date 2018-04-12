<?php

namespace CrowdStar\SVNAgent;

use CrowdStar\SVNAgent\Traits\LoggerTrait;
use Monolog\Logger;

/**
 * Class Request
 *
 * @package CrowdStar\SVNAgent
 */
class Request
{
    use LoggerTrait;

    /**
     * @var string
     */
    protected $request = '';

    /**
     * Request constructor.
     *
     * @param Logger|null $logger
     */
    public function __construct(Logger $logger = null)
    {
        $this->setLogger($logger, 'request');
    }

    /**
     * @return $this
     */
    public function readRequest(): Request
    {
        $stdin = fopen('php://stdin', 'r');
        $len   = current(unpack('L', fread($stdin, 4)));
        $this->setRequest($len ? fread($stdin, $len) : '');
        fclose($stdin);

        return $this;
    }

    /**
     * @return string
     */
    public function getRequest(): string
    {
        return $this->request;
    }

    /**
     * @param string $request
     * @return $this
     */
    public function setRequest(string $request): Request
    {
        $this->request = $request;

        $this->getLogger()->info('request: ' . $request);

        return $this;
    }
}
