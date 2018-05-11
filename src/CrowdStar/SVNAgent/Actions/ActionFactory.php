<?php

namespace CrowdStar\SVNAgent\Actions;

use CrowdStar\SVNAgent\Exceptions\ClientException;
use CrowdStar\SVNAgent\Request;
use Monolog\Logger;

/**
 * Class ActionFactory
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class ActionFactory
{
    const SVN_CLEANUP = 'cleanup';
    const SVN_COMMIT  = 'commit';
    const SVN_OPEN    = 'open';
    const SVN_REVIEW  = 'review';
    const SVN_UNLOCK  = 'unlock';
    const SVN_UPDATE  = 'update';
    const TEST_SLEEP  = 'sleep';

    const ACTION_CLASSES = [
        self::SVN_CLEANUP => Cleanup::class,
        self::SVN_COMMIT  => Commit::class,
        self::SVN_OPEN    => Open::class,
        self::SVN_REVIEW  => Review::class,
        self::SVN_UNLOCK  => Unlock::class,
        self::SVN_UPDATE  => Update::class,
        self::TEST_SLEEP  => Sleep::class,
    ];

    /**
     * @param Request $request
     * @param Logger|null $logger
     * @return AbstractAction
     * @throws ClientException
     */
    public static function fromRequest(Request $request, Logger $logger = null): AbstractAction
    {
        $action = $request->getAction();

        if (array_key_exists($action, self::ACTION_CLASSES)) {
            $class = self::ACTION_CLASSES[$action];

            return new $class($request, ($logger ?: $request->getLogger()));
        } else {
            throw new ClientException("unsupported SVN Agent action '{$action}'");
        }
    }
}
