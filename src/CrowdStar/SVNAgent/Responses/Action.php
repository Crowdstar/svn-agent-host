<?php

namespace CrowdStar\SVNAgent\Responses;

/**
 * Class Action
 *
 * @package CrowdStar\SVNAgent\Responses
 */
class Action
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $file;

    /**
     * Action constructor.
     *
     * @param string $type
     * @param string $file
     */
    public function __construct(string $type, string $file)
    {
        $this->setType($type)->setFile($file);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $file
     * @return $this
     */
    public function setType(string $type): Action
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * @param string $file
     * @return $this
     */
    public function setFile(string $file): Action
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'type' => $this->getType(),
            'file' => $this->getFile(),
        ];
    }
}
