<?php

namespace CrowdStar\SVNAgent;

use Closure;
use CrowdStar\SVNAgent\Traits\LoggerTrait;
use Monolog\Logger;
use MrRio\ShellWrap as sh;
use MrRio\ShellWrapException;

/**
 * Class Action
 *
 * @package CrowdStar\SVNAgent
 */
class Action
{
    use LoggerTrait;

    const SVN_CHECKOUT = 'checkout';
    const SVN_STATUS   = 'status';
    const SVN_CLEANUP  = 'cleanup';

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var string
     */
    protected $action;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var string
     */
    protected $message;

    /**
     * Action constructor.
     *
     * @param string $action
     * @param array $data
     * @param Logger $logger
     */
    public function __construct(string $action, array $data, Logger $logger = null)
    {
        $this
            ->setAction($action)
            ->setData($data)
            ->setLoggerName('action')
            ->setLogger($logger ?: Config::singleton()->getLogger())
            ->setConfig(Config::singleton());
    }

    /**
     * @param Request $request
     * @param Logger $logger
     * @return $this
     */
    public static function fromRequest(Request $request, Logger $logger = null): Action
    {
        $requestData = json_decode($request->getRequest(), true);
        $action      = $requestData['action'] ?? '';
        $data        = $requestData['data'] ?? [];

        return ($logger ? new static($action, $data, $logger) : new static($action, $data));
    }

    /**
     * @return Action
     */
    public function process(): Action
    {
        $this->setResponse(new Response($this->getLogger()));

        switch ($this->getAction()) {
            case self::SVN_CHECKOUT:
                if (!is_readable($this->getConfig()->getSvnRootDir())) {
                    $this->setMessage('SVN checkout')->exec(
                        function () {
                            sh::svn(
                                $this->getAction(),
                                $this->getConfig()->getSvnTrunk(),
                                $this->getConfig()->getSvnRootDir()
                            );
                        }
                    );
                } else {
                    $this->setError("Folder \"{$this->getConfig()->getSvnRootDir()}\" already exists");
                }
                break;
            case self::SVN_STATUS:
                if (is_readable($this->getConfig()->getSvnRootDir())) {
                    $this->setMessage('SVN status')->exec(
                        function () {
                            sh::svn($this->getAction(), $this->getConfig()->getSvnRootDir());
                        }
                    );
                } else {
                    $this->setError("Folder \"{$this->getConfig()->getSvnRootDir()}\" not exist");
                }
                break;
            case self::SVN_CLEANUP:
                if (is_readable($this->getConfig()->getSvnRootDir())) {
                    chdir($this->getConfig()->getSvnRootDir());
                    $this->setMessage('SVN cleanup')->exec(
                        function () {
                            sh::bash(Config::singleton()->getRootDir() . '/bin/svn-cleanup.sh');
                        }
                    );
                } else {
                    $this->setError("Folder \"{$this->getConfig()->getSvnRootDir()}\" not exist");
                }
                break;
            default:
                $this->setError("unknown action '{$this->getAction()}'");
                break;
        }

        $this->getLogger()->info('response: ' . $this->getResponse());

        return $this;
    }

    /**
     * @param Closure ...$array
     * @return Action
     */
    protected function exec(Closure ...$array): Action
    {
        $sh      = new sh();
        $results = [];
        try {
            $this->getLogger()->info("now executing command: {$this->getMessage()}");
            foreach ($array as $c) {
                $c();
                $results[] = (string) $sh;
            }
            $this->getResponse()->setResponse(implode("\n\n", $results));
        } catch (ShellWrapException $e) {
            $this->getLogger()->error("error: {$e->getMessage()}");
            $this->getResponse()->setError($e->getMessage());
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
    public function setConfig(Config $config): Action
    {
        $this->config = $config;

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
    public function setAction(string $action): Action
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
    public function setData(array $data): Action
    {
        $this->data = $data;

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
    public function setResponse(Response $response): Action
    {
        $this->response = $response;

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
    public function setMessage(string $message): Action
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @param string $error
     * @return Action
     */
    protected function setError(string $error): Action
    {
        $this->getResponse()->setError($error);

        return $this;
    }
}
