<?php

namespace CrowdStar\SVNAgent\Responses;

use CrowdStar\SVNAgent\Exceptions\Exception;
use CrowdStar\SVNAgent\Traits\LoggerTrait;

/**
 * Class ErrorResponse
 *
 * @package CrowdStar\SVNAgent\Responses
 */
class ErrorResponse extends AbstractResponse
{
    use LoggerTrait;

    /**
     * @var string
     */
    protected $error;

    /**
     * ErrorResponse constructor.
     *
     * @param string|null $error
     */
    public function __construct(string $error = null)
    {
        $this->initLogger();

        if (isset($error)) {
            $this->setError($error);
        }
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * @param string $error
     * @return $this
     */
    public function setError(string $error): AbstractResponse
    {
        $this->error = $error;
        $this->getLogger()->error('error: ' . $error);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        return [
            'success' => false,
            'error'   => $this->getError(),
        ];
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function process(string $output)
    {
        throw new Exception(
            'Class ErrorResponse should not need to use method process() to process responses by itself'
        );
    }
}
