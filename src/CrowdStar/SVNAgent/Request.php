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
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $action;

    /**
     * @var array
     */
    protected $data = [];

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
        $data  = $len ? json_decode(fread($stdin, $len), true) : [];
        fclose($stdin);

        $this
            ->setUsername($data['username'] ? base64_decode($data['username']) : '')
            ->setPassword($data['password'] ? base64_decode($data['password']) : '')
            ->setAction($data['action'] ?? '')
            ->setData($data['data'] ?? []);

        $this->getLogger()->info('request action: ' . $this->getAction());
        $this->getLogger()->info('request data: ' . json_encode($this->getData()));

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return $this
     */
    public function setUsername(string $username): Request
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function setPassword(string $password): Request
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param string $action
     * @return $this
     */
    public function setAction(string $action): Request
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setData(array $data): Request
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param string $name
     * @return mixed
     * @throws Exception
     */
    public function get(string $name)
    {
        if (!array_key_exists($name, $this->data)) {
            throw new Exception("field '{$name}' not passed in in the request");
        }

        return $this->data[$name];
    }
}
