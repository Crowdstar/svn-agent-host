<?php

namespace CrowdStar\SVNAgent\Responses;

/**
 * Class UpdateResponse
 *
 * @package CrowdStar\SVNAgent\Responses
 */
class UpdateResponse extends ReviewResponse
{
    /**
     * @var int
     */
    protected $revision;

    /**
     * @return int
     */
    public function getRevision(): int
    {
        return $this->revision;
    }

    /**
     * @param int $revision
     * @return UpdateResponse
     */
    public function setRevision(int $revision): UpdateResponse
    {
        $this->revision = $revision;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        return parent::toArray() + ['revision' => $this->getRevision()];
    }

    /**
     * @inheritdoc
     */
    public function process(string $output)
    {
        $start = "'.'";
        foreach (["\nUpdated to revision ", "\nAt revision "] as $needle) {
            if (strpos($output, $needle) !== false) {
                $end = $needle;
                break;
            }
        }

        if (isset($end)) {
            $lines = $this->toLines(
                substr(
                    $output,
                    strpos($output, $start) + strlen($start),
                    strpos($output, $end) - (strpos($output, $start) + strlen($start))
                )
            );

            array_map(
                function ($line) {
                    if (preg_match('/^\s*([^\s]+)\s*([^\s].*)\s*$/', $line, $matches)) {
                        $this->addAction(new Action($matches[1], $matches[2]));
                    }
                },
                $lines
            );

            if (preg_match('/' . preg_quote($end) . '([\d]+)\.$/m', $output, $matches)) {
                $this->setRevision(intval($matches[1]));
            } else {
                //TODO: report error to the developers.
                $this->setRevision(-1);
            }
        } else {
            //TODO: report error to the developers.
            $this->setRevision(-1);
        }

        return $this;
    }
}
