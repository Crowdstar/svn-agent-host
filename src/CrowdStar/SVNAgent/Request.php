<?php

namespace CrowdStar\SVNAgent;

use CrowdStar\SVNAgent\Exceptions\ClientException;
use CrowdStar\SVNAgent\Traits\LoggerTrait;
use Psr\Log\LoggerInterface;

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
     * Timeout for running lockable action through method \CrowdStar\SVNAgent\Actions\AbstractAction::process().
     *
     * @var int
     * @see \CrowdStar\SVNAgent\Actions\AbstractAction::process()
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
     * @param LoggerInterface|null $logger
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->setLogger($logger);
    }

    /**
     * @param LoggerInterface|null $logger
     * @return Request
     */
    public static function readRequest(LoggerInterface $logger = null): Request
    {
        $request = new static($logger);

        $stdin = fopen('php://stdin', 'r');
        $len   = current(unpack('L', fread($stdin, 4)));
        $data  = $len ? json_decode(fread($stdin, $len), true) : [];
        fclose($stdin);

        $request->init($data);

        return $request;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function init(array $data): Request
    {
        $this
            ->setUsername($data['username'] ? base64_decode($data['username']) : '')
            ->setPassword($data['password'] ? base64_decode($data['password']) : '')
            ->setTimeout($data['timeout'] ? $data['timeout'] : Config::DEFAULT_TIMEOUT)
            ->setAction($data['action'] ?? '')
            ->setData($data['data'] ?? []);

        $this->getLogger()->info(
            'action requested',
            [
                'action'  => $this->getAction(),
                'timeout' => $this->getTimeout(),
            ]
        );
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
