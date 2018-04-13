<?php

namespace CrowdStar\SVNAgent;

use CrowdStar\SVNAgent\Traits\LoggerTrait;
use Dotenv\Dotenv;
use Exception;
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

    const SVN_AGENT_ROOT_DIR     = 'SVN_AGENT_ROOT_DIR';
    const SVN_AGENT_SVN_ROOT_DIR = 'SVN_AGENT_SVN_ROOT_DIR';
    const SVN_AGENT_SVN_ROOT     = 'SVN_AGENT_SVN_ROOT';
    const SVN_AGENT_SVN_TRUNK    = 'SVN_AGENT_SVN_TRUNK';
    const SVN_AGENT_LOGFILE      = 'SVN_AGENT_LOGFILE';
    const SVN_AGENT_TIMEZONE     = 'SVN_AGENT_TIMEZONE';

    const REQUIRED_ENVIRONMENT_VARIABLES = [
        self::SVN_AGENT_ROOT_DIR,
        self::SVN_AGENT_SVN_ROOT_DIR,
        self::SVN_AGENT_SVN_ROOT,
        self::SVN_AGENT_SVN_TRUNK,
        self::SVN_AGENT_LOGFILE,
        self::SVN_AGENT_TIMEZONE,
    ];

    /**
     * @var Config
     */
    protected static $singleton;

    /**
     * Config constructor.
     */
    protected function __construct()
    {
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
     * @param string $path
     * @return Config
     * @throws Exception
     */
    public function init(string $path): Config
    {
        $dotenv = (new Dotenv($path));
        $dotenv->overload();
        foreach (self::REQUIRED_ENVIRONMENT_VARIABLES as $var) {
            $dotenv->required($var)->notEmpty();
        }

        date_default_timezone_set(getenv(self::SVN_AGENT_TIMEZONE));

        $logger = new Logger('SVN Agent');
        $logger->pushHandler(new StreamHandler(getenv(self::SVN_AGENT_LOGFILE), Logger::DEBUG));
        $this->setLogger($logger);

        ErrorHandler::register($this->getLogger());

        return $this;
    }

    /**
     * @return string
     */
    public function getRootDir(): string
    {
        return $this->rtrim(getenv(self::SVN_AGENT_ROOT_DIR));
    }

    /**
     * @return string
     */
    public function getSvnRootDir(): string
    {
        return $this->rtrim(getenv(self::SVN_AGENT_SVN_ROOT_DIR));
    }

    /**
     * @return string
     */
    public function getSvnRoot(): string
    {
        return $this->rtrim(getenv(self::SVN_AGENT_SVN_ROOT));
    }

    /**
     * @return string
     */
    public function getSvnTrunk(): string
    {
        return $this->rtrim(getenv(self::SVN_AGENT_SVN_TRUNK));
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
