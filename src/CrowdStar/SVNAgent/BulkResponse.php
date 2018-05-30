<?php

namespace CrowdStar\SVNAgent;

/**
 * Class BulkResponse
 *
 * @package CrowdStar\SVNAgent
 */
class BulkResponse extends AbstractResponse
{
    /**
     * @var Response[]
     */
    protected $responses = [];

    /**
     * @return Response[]
     */
    public function getResponses(): array
    {
        return $this->responses;
    }

    /**
     * @param Response[] $responses
     * @return $this
     */
    public function setResponses(array $responses): BulkResponse
    {
        $this->responses = $responses;
    }

    /**
     * @param Response $response
     * @return $this
     */
    public function addResponse(Response $response): BulkResponse
    {
        $this->responses[] = $response;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        $data = [];
        if ($this->hasError()) {
            $data['error'] = $this->getError();
        } else {
            $hasError = array_reduce(
                $this->getResponses(),
                function (bool $carry, Response $response) {
                    return ($carry || $response->hasError());
                },
                false
            );
            if ($hasError) {
                $data['error'] = Error::BULK_FAILED;
            }

            $data['response'] = array_map(
                function (Response $response) {
                    return $response->toArray();
                },
                $this->getResponses()
            );
        }

        return $data;
    }
}
