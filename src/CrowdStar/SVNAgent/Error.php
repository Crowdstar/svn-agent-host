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

    const ERRORS = [
        self::LOCK_FAILED => 'failed to gain lock',
    ];
}
