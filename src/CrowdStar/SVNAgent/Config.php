<?php

namespace CrowdStar\SVNAgent;

use Bugsnag\Client;
use Bugsnag\Handler;
use Bugsnag\Report;
use CrowdStar\SVNAgent\Traits\LoggerTrait;
use Dotenv\Dotenv;
use Exception;
use Monolog\ErrorHandler;
use Monolog\Formatter\LineFormatter;
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
     * The current system version.
     */
    const VERSION = '0.1.0';

    /**
     * Default timeout to execute given action. In seconds.
     */
    const DEFAULT_TIMEOUT = 60;

    const SVN_AGENT_ROOT_DIR        = 'SVN_AGENT_ROOT_DIR';
    const SVN_AGENT_SVN_ROOT_DIR    = 'SVN_AGENT_SVN_ROOT_DIR';
    const SVN_AGENT_SVN_ROOT        = 'SVN_AGENT_SVN_ROOT';
    const SVN_AGENT_MUTEX_NAME      = 'SVN_AGENT_MUTEX_NAME';
    const SVN_AGENT_LOGFILE         = 'SVN_AGENT_LOGFILE';
    const SVN_AGENT_BUGSNAG_API_KEY = 'SVN_AGENT_BUGSNAG_API_KEY';
    const SVN_AGENT_EXTENSION_ID    = 'SVN_AGENT_EXTENSION_ID';
    const SVN_AGENT_TIMEZONE        = 'SVN_AGENT_TIMEZONE';

    const REQUIRED_ENVIRONMENT_VARIABLES = [
        self::SVN_AGENT_ROOT_DIR,
        self::SVN_AGENT_SVN_ROOT_DIR,
        self::SVN_AGENT_SVN_ROOT,
        self::SVN_AGENT_MUTEX_NAME,
        self::SVN_AGENT_LOGFILE,
        self::SVN_AGENT_EXTENSION_ID,
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

        $logger  = new Logger(getmypid());
        $handler = new StreamHandler(getenv(self::SVN_AGENT_LOGFILE), Logger::DEBUG);
        $handler->setFormatter(new LineFormatter(null, null, false, true));
        $logger->pushHandler($handler);
        $this->setLogger($logger);

        ErrorHandler::register($this->getLogger());

        // Report errors to Bugsnag only if the host program is invoked from specific Chrome extension.
        if ((2 == $_SERVER['argc'])
            && ("chrome-extension://{$this->getExtensionId()}/" == $_SERVER['argv'][1])
            && getenv(self::SVN_AGENT_BUGSNAG_API_KEY)) {
            $bugsnag = Client::make(getenv(self::SVN_AGENT_BUGSNAG_API_KEY));
            $bugsnag->registerCallback(
                function (Report $report) {
                    $report->setUser(
                        [
                            'user' => $_SERVER['USER'],
                        ]
                    );
                }
            );
            Handler::registerWithPrevious($bugsnag);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getRootDir(): string
    {
        return PathHelper::rtrim(getenv(self::SVN_AGENT_ROOT_DIR));
    }

    /**
     * @return string
     */
    public function getSvnRootDir(): string
    {
        return PathHelper::rtrim(getenv(self::SVN_AGENT_SVN_ROOT_DIR));
    }

    /**
     * @return string
     */
    public function getSvnRoot(): string
    {
        return PathHelper::rtrim(getenv(self::SVN_AGENT_SVN_ROOT));
    }

    /**
     * @return string
     */
    public function getExtensionId(): string
    {
        return getenv(self::SVN_AGENT_EXTENSION_ID);
    }
}
