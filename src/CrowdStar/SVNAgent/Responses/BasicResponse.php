<?php

namespace CrowdStar\SVNAgent\Responses;

/**
 * Class BasicResponse
 *
 * @package CrowdStar\SVNAgent\Responses
 */
class BasicResponse extends AbstractResponse
{
    /**
     * @var string
     */
    protected $message = '';

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message): BasicResponse
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        return [
            'success' => true,
            'message' => $this->getMessage(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function process(string $output)
    {
        $this->setMessage($output);

        return $this;
    }
}
