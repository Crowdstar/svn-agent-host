<?php

namespace CrowdStar\SVNAgent;

use CrowdStar\SVNAgent\Traits\LoggerTrait;
use Monolog\Logger;

/**
 * Class Response
 *
 * @package CrowdStar\SVNAgent
 */
class Response
{
    use LoggerTrait;

    /**
     * @var string
     */
    protected $response = '';

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
    public function sendResponse(): Response
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
    public function getResponse(): string
    {
        return $this->response;
    }

    /**
     * @param string $response
     * @return $this
     */
    public function setResponse(string $response): Response
    {
        $this->response = $response;

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
    public function setError(string $error): Response
    {
        $this->error = $error;
        $this->getLogger()->error('error: ' . $error);

        return $this;
    }

    /**
     * @return bool
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
        $data = [];
        if ($this->hasError()) {
            $data['error'] = $this->getError();
        } else {
            $data['response'] = $this->getResponse();
        }

        return json_encode($data);
    }
}
