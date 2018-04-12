<?php

namespace CrowdStar\SVNAgent;

use CrowdStar\SVNAgent\Traits\LoggerTrait;
use Monolog\ErrorHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Class Config
 *
 * @package CrowdStar\SVNAgent
 */
class Config
{
    use LoggerTrait;

    /**
     * @var string
     */
    protected $rootDir;

    /**
     * @var string
     */
    protected $svnRootDir;

    /**
     * @var string
     */
    protected $svnRoot;

    /**
     * @var string
     */
    protected $svnTrunk;

    /**
     * @var Config
     */
    protected static $singleton;

    /**
     * Config constructor.
     */
    protected function __construct()
    {
        $this->init();
    }

    /**
     * Not allow to clone the object since there should be only one instance of it there.
     */
    protected function __clone()
    {
    }

    /**
     * @return $this
     */
    public static function singleton(): Config
    {
        if (!isset(self::$singleton)) {
            self::$singleton = new static();
        }

        return self::$singleton;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function init(): Config
    {
        date_default_timezone_set('America/Los_Angeles');

        $this
            ->setRootDir($_SERVER['HOME'] . DIRECTORY_SEPARATOR . 'svn-agent')
            ->setSvnRootDir($this->getRootDir() . '/svn-agent')
            ->setSvnRoot('https://svn.riouxsvn.com/deminy')
            ->setSvnTrunk($this->getSvnRoot() . '/trunk');

        $logger = new Logger('SVN Agent');
        $logger->pushHandler(
            new StreamHandler($this->getRootDir() . DIRECTORY_SEPARATOR . 'svn-agent-host.log', Logger::DEBUG)
        );
        $this->setLogger($logger);

        ErrorHandler::register($this->getLogger());

        return $this;
    }

    /**
     * @return string
     */
    public function getRootDir(): string
    {
        return $this->rootDir;
    }

    /**
     * @param string $rootDir
     * @return $this
     */
    public function setRootDir(string $rootDir): Config
    {
        $this->rootDir = $this->rtrim($rootDir);

        return $this;
    }

    /**
     * @return string
     */
    public function getSvnRootDir(): string
    {
        return $this->svnRootDir;
    }

    /**
     * @param string $svnRootDir
     * @return $this
     */
    public function setSvnRootDir(string $svnRootDir): Config
    {
        $this->svnRootDir = $this->rtrim($svnRootDir);

        return $this;
    }

    /**
     * @return string
     */
    public function getSvnRoot(): string
    {
        return $this->svnRoot;
    }

    /**
     * @param string $svnRoot
     * @return $this
     */
    public function setSvnRoot(string $svnRoot): Config
    {
        $this->svnRoot = $this->rtrim($svnRoot);

        return $this;
    }

    /**
     * @return string
     */
    public function getSvnTrunk(): string
    {
        return $this->svnTrunk;
    }

    /**
     * @param string $svnTrunk
     * @return $this
     */
    public function setSvnTrunk(string $svnTrunk): Config
    {
        $this->svnTrunk = $this->rtrim($svnTrunk);

        return $this;
    }

    /**
     * @param string $dir
     * @return string
     */
    protected function rtrim(string $dir): string
    {
        return rtrim($dir, DIRECTORY_SEPARATOR);
    }
}
