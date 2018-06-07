<?php

namespace CrowdStar\SVNAgent\Responses;

use CrowdStar\SVNAgent\Exceptions\ClientException;
use CrowdStar\SVNAgent\Traits\PathTrait;

/**
 * Class AbstractPathBasedResponse
 *
 * @package CrowdStar\SVNAgent\Responses
 */
abstract class AbstractPathBasedResponse extends AbstractResponse
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
}
