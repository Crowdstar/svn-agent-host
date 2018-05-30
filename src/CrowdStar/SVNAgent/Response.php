<?php

namespace CrowdStar\SVNAgent;

/**
 * Class Response
 *
 * @package CrowdStar\SVNAgent
 */
class Response extends AbstractResponse
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
    public function setMessage(string $message): Response
    {
        $this->message = $message;

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
            $data['response'] = $this->getMessage();
        }

        return $data;
    }
}
