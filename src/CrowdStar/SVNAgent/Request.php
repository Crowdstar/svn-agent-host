<?php

namespace CrowdStar\SVNAgent;

use CrowdStar\SVNAgent\Exceptions\ClientException;
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
     * @var int
     */
    protected $timeout;

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
        $this->setLogger($logger);
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
            ->setTimeout($data['timeout'] ? $data['timeout'] : Config::DEFAULT_TIMEOUT)
            ->setAction($data['action'] ?? '')
            ->setData($data['data'] ?? []);

        // As soon as we have request data parsed, we set timeout accordingly.
        set_time_limit($this->getTimeout());

        $this->getLogger()->info('action requested', ['action' => $this->getAction(), 'timeout' => $this->timeout]);
        if ($this->getData()) {
            $this->getLogger()->info('request data received', $this->getData());
        } else {
            $this->getLogger()->info('request has no additional data include');
        }

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
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     * @return $this
     */
    public function setTimeout(int $timeout): Request
    {
        $this->timeout = $timeout;

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
     * @throws ClientException
     */
    public function get(string $name)
    {
        if (!array_key_exists($name, $this->data)) {
            throw new ClientException("field '{$name}' not passed in in the request");
        }

        return $this->data[$name];
    }
}
