<?php

namespace CrowdStar\SVNAgent\Responses;

use CrowdStar\SVNAgent\Exceptions\Exception;

/**
 * Class BulkResponse
 *
 * @package CrowdStar\SVNAgent\Responses
 */
class BulkResponse extends AbstractResponse
{
    /**
     * @var AbstractResponse[]
     */
    protected $responses = [];

    /**
     * @return AbstractResponse[]
     */
    public function getResponses(): array
    {
        return $this->responses;
    }

    /**
     * @param AbstractResponse[] $responses
     * @return $this
     */
    public function setResponses(array $responses): BulkResponse
    {
        $this->responses = $responses;
    }

    /**
     * @param AbstractResponse $response
     * @return $this
     */
    public function addResponse(AbstractResponse $response): BulkResponse
    {
        $this->responses[] = $response;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        return [
            'success'  => true,
            'response' => array_map(
                function (AbstractResponse $response) {
                    return $response->toArray();
                },
                $this->getResponses()
            ),
        ];
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function process(string $output)
    {
        throw new Exception('Bulk actions should not need to use method process() to process responses by itself');
    }
}
