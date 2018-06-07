<?php

namespace CrowdStar\SVNAgent;

/**
 * Class Error
 *
 * @package CrowdStar\SVNAgent
 */
class Error
{
    const LOCK_FAILED     = 'e2801';
    const SERVER_OUTDATED = 'e2811';
    const CLIENT_OUTDATED = 'e2812';

    const ERRORS = [
        self::LOCK_FAILED     => 'failed to gain lock',
        self::SERVER_OUTDATED => 'messaging host upgrade required',
        self::CLIENT_OUTDATED => 'Chrome extension upgrade required',
    ];
}
