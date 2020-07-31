<?php
/**************************************************************************
 * Copyright 2018 Glu Mobile Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *************************************************************************/

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
    const SELF_UPDATE            = 'self-update';
    const SVN_AUTH               = 'auth';
    const SVN_BULK_REVIEW        = 'bulk-review';
    const SVN_BULK_UPDATE        = 'bulk-update';
    const SVN_CLEANUP            = 'cleanup';
    const SVN_COMMIT             = 'commit';
    const SVN_COMMITS            = 'commits';
    const SVN_CREATE             = 'create';
    const SVN_DELETE             = 'delete';
    const SVN_EXIST              = 'exist';
    const SVN_EXPORT             = 'export';
    const SVN_OPEN               = 'open';
    const SVN_RENAME             = 'rename';
    const SVN_REVIEW             = 'review';
    const SVN_UNLOCK             = 'unlock';
    const SVN_UPDATE             = 'update';
    const SVN_UPDATE_CREDENTIALS = 'update-credentials';
    const TEST_IDLE              = 'idle';
    const VERSION                = 'version';

    const ACTION_CLASSES = [
        self::SELF_UPDATE            => SelfUpdate::class,
        self::SVN_AUTH               => Auth::class,
        self::SVN_BULK_REVIEW        => BulkReview::class,
        self::SVN_BULK_UPDATE        => BulkUpdate::class,
        self::SVN_CLEANUP            => Cleanup::class,
        self::SVN_COMMIT             => Commit::class,
        self::SVN_COMMITS            => BulkCommits::class,
        self::SVN_CREATE             => Create::class,
        self::SVN_DELETE             => Delete::class,
        self::SVN_EXIST              => Exist::class,
        self::SVN_EXPORT             => Export::class,
        self::SVN_OPEN               => Open::class,
        self::SVN_RENAME             => Rename::class,
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
