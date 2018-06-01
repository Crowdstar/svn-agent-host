<?php

namespace CrowdStar\SVNAgent\Responses;

use CrowdStar\SVNAgent\Exceptions\ClientException;
use CrowdStar\SVNAgent\Traits\PathTrait;

/**
 * Class PathBasedResponse
 *
 * @package CrowdStar\SVNAgent\Responses
 */
class PathBasedResponse extends AbstractResponse
{
    use PathTrait;

    /**
     * CommitResponse constructor.
     *
     * @param string|null $path
     * @throws ClientException
     */
    public function __construct(string $path)
    {
        $this->setPath($path);
    }

    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        return [
            'success' => true,
            'path'    => $this->getPath(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function process(string $output)
    {
        // Path-based response doesn't not need to process outputs by itself.
        return $this;
    }
}
