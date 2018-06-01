<?php

namespace CrowdStar\SVNAgent\Responses;

use CrowdStar\SVNAgent\Traits\ResponseActionsTrait;

/**
 * Class ReviewResponse
 *
 * @package CrowdStar\SVNAgent\Responses
 */
class ReviewResponse extends PathBasedResponse
{
    use ResponseActionsTrait;

    /**
     * @inheritdoc
     */
    public function __construct(string $path)
    {
        parent::__construct($path);
        $this->setActions(new Actions());
    }

    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        return parent::toArray() + ['actions' => $this->getActions()->toArray()];
    }

    /**
     * @inheritdoc
     */
    public function process(string $output)
    {
        array_map(
            function ($line) {
                if (preg_match('/^\s*([^\s]+)\s*([^\s].*)\s*$/', trim($line), $matches)) {
                    $this->addAction(new Action($matches[1], $matches[2]));
                }
            },
            $this->toLines($output)
        );

        return $this;
    }

    /**
     * @param string $output
     * @return array
     */
    protected function toLines(string $output): array
    {
        return array_filter(array_map('trim', explode("\n", $output)));
    }
}
