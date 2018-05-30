<?php

namespace CrowdStar\SVNAgent;

/**
 * Class Error
 *
 * @package CrowdStar\SVNAgent
 */
class Error
{
    const LOCK_FAILED = 'e2801';
    const BULK_FAILED = 'e2811';

    const ERRORS = [
        self::LOCK_FAILED => 'failed to gain lock',
        self::BULK_FAILED => 'bulk action failed',
    ];
}
