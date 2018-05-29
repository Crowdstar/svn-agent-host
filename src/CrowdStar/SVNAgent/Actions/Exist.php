<?php

namespace CrowdStar\SVNAgent\Actions;

use CrowdStar\SVNAgent\SVNHelper;

/**
 * Class Exist
 *
 * @package CrowdStar\SVNAgent\Actions
 */
class Exist extends AbstractAction
{
    const TYPE_FOLDER = 'folder';
    const TYPE_URL    = 'url';

    /**
     * @inheritdoc
     */
    public function processAction(): AbstractAction
    {
        $type = $this->getRequest()->get('type');
        switch ($type) {
            case self::TYPE_FOLDER:
                $dir = $this->getSvnDir();
                if (is_dir($dir)) {
                    $this->setResponseMessage("Folder {$dir} exists");
                } else {
                    $this->setError("Folder {$dir} not exists");
                }
                break;
            case self::TYPE_URL:
                $url = $this->getSvnUri();
                if (SVNHelper::urlExists($url, $this->getRequest())) {
                    $this->setResponseMessage("URL {$url} exists");
                } else {
                    $this->setError("URL {$url} not exists");
                }
                break;
            default:
                $this->setError("invalid type '{$type}'");
                break;
        }

        return $this;
    }
}
