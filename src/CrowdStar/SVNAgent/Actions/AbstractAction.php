<?php

namespace CrowdStar\SVNAgent\Actions;

use Closure;
use CrowdStar\SVNAgent\Config;
use CrowdStar\SVNAgent\Error;
use CrowdStar\SVNAgent\Exceptions\ClientException;
use CrowdStar\SVNAgent\Exceptions\Exception;
use CrowdStar\SVNAgent\Request;
use CrowdStar\SVNAgent\Responses\AbstractResponse;
use CrowdStar\SVNAgent\Responses\ErrorResponse;
use CrowdStar\SVNAgent\Responses\PathBasedErrorResponse;
use CrowdStar\SVNAgent\Responses\Response;
use CrowdStar\SVNAgent\SVNHelper;
use CrowdStar\SVNAgent\Traits\LoggerTrait;
use CrowdStar\SVNAgent\Traits\PathTrait;
use Monolog\Logger;
use MrRio\ShellWrap;
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
    use LoggerTrait, PathTrait;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var AbstractResponse
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
     * @param AbstractResponse|null $response
     * @param Logger|null $logger
     * @throws ClientException
     */
    public function __construct(Request $request, AbstractResponse $response = null, Logger $logger = null)
    {
        $this
            ->setConfig(Config::singleton())
            ->setLogger(($logger ?: $request->getLogger()))
            ->setRequest($request)
            ->init();
    }

    /**
     * @return AbstractResponse
     */
    public function run(): AbstractResponse
    {
        try {
            $this->process()->getResponse();
        } catch (ClientException $e) {
            $this->getResponse()->setError($e->getMessage());
        } catch (Exception $e) {
            $this->getResponse()->setError(
                'Backend issue. Please check with Home backend developers for helps.'
            );
            $this->getLogger()->error(get_class($e) . ': ' . $e->getMessage());
        } catch (\Exception $e) {
            $this->getResponse()->setError(
                'Unknown issue. Please check with Home backend developers for helps.'
            );
            $this->getLogger()->error(get_class($e) . ': ' . $e->getMessage());
        }

        return $this->getResponse();
    }

    /**
     * @return AbstractAction
     * @throws ClientException
     */
    public function process(): AbstractAction
    {
        if ($this instanceof LocklessActionInterface) {
            $this->processAction();
        } else {
            set_time_limit($this->getRequest()->getTimeout());

            $mutex = $this->getMutex();
            if ($mutex->acquireLock(0)) {
                $this->processAction();
                $mutex->releaseLock();
            } else {
                $this->setError(Error::LOCK_FAILED);
            }
        }

        // Process post actions once current action is processed.
        // TODO: handle response messages/errors properly when multiple actions there.
        foreach ($this->getPostActions() as $action) {
            $action->process();
        }

        $this->getLogger()->info('response: ' . $this->getResponse());

        return $this;
    }

    /**
     * @return AbstractAction
     */
    abstract public function processAction(): AbstractAction;

    /**
     * @param Closure $closure
     * @return AbstractAction
     * @throws Exception
     */
    protected function exec(Closure $closure): AbstractAction
    {
        if (!empty($this->getMessage())) {
            $this->getLogger()->info("now executing command: {$this->getMessage()}");
        } else {
            $this->getLogger()->info("now executing command defined in class " . __CLASS__);
        }

        $sh = new ShellWrap();
        try {
            $closure();
            $results[] = (string) $sh;
            $this->prepareResponse((string) $sh);
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
     */
    public function setRequest(Request $request): AbstractAction
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return AbstractResponse
     */
    public function getResponse(): AbstractResponse
    {
        return $this->response;
    }

    /**
     * @param AbstractResponse $response
     * @return $this
     */
    public function setResponse(AbstractResponse $response): AbstractAction
    {
        $this->response = $response;

        return $this;
    }

    /**
     * @param string $output
     * @return $this
     * @throws Exception
     */
    protected function prepareResponse(string $output): AbstractAction
    {
        $this->getResponse()->process($output);

        return $this;
    }

    /**
     * @return string
     */
    protected function getSvnUri(): string
    {
        return $this->getConfig()->getSvnRoot() . $this->getPath();
    }

    /**
     * @return string
     */
    protected function getSvnDir(): string
    {
        return $this->getConfig()->getSvnRootDir() . $this->getPath();
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
     * @return Mutex
     */
    protected function getMutex(): Mutex
    {
        return new Mutex(getenv(Config::SVN_AGENT_MUTEX_NAME), new FlockLock($this->getConfig()->getRootDir()));
    }

    /**
     * @param string $error
     * @return AbstractAction
     * @throws ClientException
     */
    protected function setError(string $error): AbstractAction
    {
        if ($this instanceof PathBasedActionInterface) {
            $this->setResponse((new PathBasedErrorResponse($error))->setPath($this->getPath()));
        } else {
            $this->setResponse(new ErrorResponse($error));
        }

        return $this;
    }

    /**
     * @return bool
     */
    protected function hasError(): bool
    {
        return ($this->getResponse() instanceof ErrorResponse);
    }

    /**
     * @return AbstractAction
     */
    abstract protected function initResponse(): AbstractAction;

    /**
     * @return $this
     * @throws ClientException
     */
    protected function init(): AbstractAction
    {
        if (!($this instanceof PathNotRequiredActionInterface)) {
            $this->setPath($this->getRequest()->get('path'));
        }

        return $this->validate()->initResponse();
    }

    /**
     * @return $this
     * @throws ClientException
     */
    protected function validate(): AbstractAction
    {
        if (!$this->getRequest()->getUsername() || !$this->getRequest()->getPassword()) {
            throw new ClientException('SVN credential missing');
        }

        if (!$this->path && !($this instanceof PathNotRequiredActionInterface)) {
            throw new ClientException('field "path" not passed in as should');
        }

        return $this;
    }

    /**
     * @return AbstractAction
     * @throws ClientException
     */
    protected function initializeSvnPathWhenNeeded(): AbstractAction
    {
        if (!SVNHelper::pathExists($this->getSvnDir())) {
            //TODO: better error handling when calling other actions from current action.
            (new Create($this->getRequest(), new Response($this->getLogger()), $this->getLogger()))
                ->processAction();
            (new Update($this->getRequest(), new Response($this->getLogger()), $this->getLogger()))
                ->processAction();
        }

        return $this;
    }

    /**
     * @return AbstractAction[]
     */
    protected function getPostActions(): array
    {
        return [];
    }
}
