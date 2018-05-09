<?php

namespace CrowdStar\SVNAgent\Actions;

use CrowdStar\SVNAgent\Exception;
use CrowdStar\SVNAgent\Request;
use Monolog\Logger;

/**
 * Class ActionFactory
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class ActionFactory
{
    const SVN_CHECKOUT = 'checkout';
    const SVN_STATUS   = 'status';
    const SVN_CLEANUP  = 'cleanup';
    const TEST_SLEEP   =  'sleep';

    const ACTION_CLASSES = [
        self::SVN_CHECKOUT => Checkout::class,
        self::SVN_STATUS   => Status::class,
        self::SVN_CLEANUP  => Cleanup::class,
        self::TEST_SLEEP   => Sleep::class,
    ];

    /**
     * @param Request $request
     * @param Logger|null $logger
     * @return AbstractAction
     * @throws Exception
     */
    public static function fromRequest(Request $request, Logger $logger = null): AbstractAction
    {
        $action = $request->getAction();

        if (array_key_exists($action, self::ACTION_CLASSES)) {
            $class = self::ACTION_CLASSES[$action];

            return new $class($request, ($logger ?: $request->getLogger()));
        } else {
            throw new Exception("class not found for SVN Agent action '{$action}'");
        }
    }
}
