<?php

namespace CrowdStar\SVNAgent\Traits;

use CrowdStar\SVNAgent\Exceptions\ClientException;
use CrowdStar\SVNAgent\PathHelper;

/**
 * Trait PathTrait
 *
 * @package CrowdStar\SVNAgent\Traits
 */
trait PathTrait
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }


    /**
     * @param string $path
     * @return $this
     * @throws ClientException
     */
    protected function setPath(string $path)
    {
        $path = PathHelper::trim($path);
        if (empty($path)) {
            throw new ClientException('SVN path is empty');
        }

        // SVN URL like https://svn.apache.org/repos/asf (without trailing slash) returns HTTP 301 response back.
        // Here we make sure there are always slashes before and after given SVN path.
        $this->path = DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR;

        return $this;
    }
}
