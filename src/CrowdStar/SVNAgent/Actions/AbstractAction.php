<?php

namespace CrowdStar\SVNAgent\Actions;

use Closure;
use CrowdStar\SVNAgent\Config;
use CrowdStar\SVNAgent\Error;
use CrowdStar\SVNAgent\Exception;
use CrowdStar\SVNAgent\Request;
use CrowdStar\SVNAgent\Response;
use CrowdStar\SVNAgent\Traits\LoggerTrait;
use Monolog\Logger;
use MrRio\ShellWrap as sh;
use MrRio\ShellWrapException;
use NinjaMutex\Lock\FlockLock;
use NinjaMutex\Mutex;

/**
 * Class AbstractAction
 *
 * @package CrowdStar\SVNAgent\Actions
 */
abstract class AbstractAction
{
    use LoggerTrait;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var string
     */
    protected $message;

    /**
     * AbstractAction constructor.
     *
     * @param Request $request
     * @param Logger|null $logger
     * @throws Exception
     */
    public function __construct(Request $request, Logger $logger = null)
    {
        $this
            ->setRequest($request)
            ->setLogger(($logger ?: $request->getLogger()), 'action')
            ->setConfig(Config::singleton());
    }

    /**
     * @return AbstractAction
     */
    public function process(): AbstractAction
    {
        $this->setResponse(new Response($this->getLogger()));

        $mutex = new Mutex('svn-agent', new FlockLock($this->getConfig()->getRootDir()));
        if ($mutex->acquireLock(0)) {
            $this->processAction();
            $mutex->releaseLock();
        } else {
            $this->setError(Error::LOCK_FAILED);
        }

        $this->getLogger()->info('response: ' . $this->getResponse());

        return $this;
    }

    /**
     * @return AbstractAction
     */
    abstract public function processAction(): AbstractAction;

    /**
     * @param Closure ...$array
     * @return AbstractAction
     */
    protected function exec(Closure ...$array): AbstractAction
    {
        $sh      = new sh();
        $results = [];
        try {
            $this->getLogger()->info("now executing command: {$this->getMessage()}");
            foreach ($array as $c) {
                $c();
                $results[] = (string) $sh;
            }
            $this->setResponseMessage(implode("\n\n", $results));
        } catch (ShellWrapException $e) {
            $this->setError($e->getMessage());
        }

        return $this;
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * @param Config $config
     * @return $this
     */
    public function setConfig(Config $config): AbstractAction
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @param Request $request
     * @return $this
     * @throws Exception
     */
    public function setRequest(Request $request): AbstractAction
    {
        if (!$request->getUsername() || !$request->getPassword()) {
            throw new Exception('SVN credential missing');
        }

        $this->request = $request;

        return $this;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * @param Response $response
     * @return $this
     */
    public function setResponse(Response $response): AbstractAction
    {
        $this->response = $response;

        return $this;
    }

    /**
     * @param string $responseMessage
     * @return $this
     */
    protected function setResponseMessage(string $responseMessage): AbstractAction
    {
        $this->getResponse()->setResponse($responseMessage);

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message): AbstractAction
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @param string $error
     * @return $this
     */
    protected function setError(string $error): AbstractAction
    {
        $this->getResponse()->setError($error);

        return $this;
    }
}
