<?php

namespace CrowdStar\SVNAgent\Actions;

use CrowdStar\SVNAgent\Exceptions\ClientException;
use CrowdStar\SVNAgent\Request;
use Psr\Log\LoggerInterface;

/**
 * Class ActionFactory
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class ActionFactory
{
    const SVN_BULK_REVIEW        = 'bulk-review';
    const SVN_BULK_UPDATE        = 'bulk-update';
    const SVN_CLEANUP            = 'cleanup';
    const SVN_COMMIT             = 'commit';
    const SVN_COMMITS            = 'commits';
    const SVN_CREATE             = 'create';
    const SVN_EXIST              = 'exist';
    const SVN_OPEN               = 'open';
    const SVN_REVIEW             = 'review';
    const SVN_UNLOCK             = 'unlock';
    const SVN_UPDATE             = 'update';
    const SVN_UPDATE_CREDENTIALS = 'update-credentials';
    const TEST_IDLE              = 'idle';
    const VERSION                = 'version';

    const ACTION_CLASSES = [
        self::SVN_BULK_REVIEW        => BulkReview::class,
        self::SVN_BULK_UPDATE        => BulkUpdate::class,
        self::SVN_CLEANUP            => Cleanup::class,
        self::SVN_COMMIT             => Commit::class,
        self::SVN_COMMITS            => BulkCommits::class,
        self::SVN_CREATE             => Create::class,
        self::SVN_EXIST              => Exist::class,
        self::SVN_OPEN               => Open::class,
        self::SVN_REVIEW             => Review::class,
        self::SVN_UNLOCK             => Unlock::class,
        self::SVN_UPDATE             => Update::class,
        self::SVN_UPDATE_CREDENTIALS => UpdateCredentials::class,
        self::TEST_IDLE              => Idle::class,
        self::VERSION                => Version::class,
    ];

    /**
     * @param Request $request
     * @param LoggerInterface|null $logger
     * @return AbstractAction
     * @throws ClientException
     */
    public static function fromRequest(Request $request, LoggerInterface $logger = null): AbstractAction
    {
        $action = $request->getAction();

        if (array_key_exists($action, self::ACTION_CLASSES)) {
            $class = self::ACTION_CLASSES[$action];

            return new $class($request, null, ($logger ?: $request->getLogger()));
        } else {
            throw new ClientException("unsupported SVN Agent action '{$action}'");
        }
    }
}
