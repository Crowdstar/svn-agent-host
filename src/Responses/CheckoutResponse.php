<?php
/**************************************************************************
 * Copyright 2018 Glu Mobile Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *************************************************************************/

namespace CrowdStar\SVNAgent\Responses;

/**
 * Class CheckoutResponse
 *
 * @package CrowdStar\SVNAgent\Responses
 */
class CheckoutResponse extends AbstractVersionedResponse
{
    /**
     * @inheritdoc
     */
    public function process(string $output)
    {
        $needle = "Checked out revision ";
        if (strpos($output, $needle) !== false) {
            if (preg_match('/Checked out revision ([\d]+)\.$/m', trim($output), $matches)) {
                $this->setRevision(intval($matches[1]));
            } else {
                $this->getLogger()->error("unable to fetch revision # from output when doing SVN checkout: {$output}");
                $this->setRevision(-1);
            }

            $lines = $this->toLines(substr($output, 0, strpos($output, $needle)));
            array_map(
                function ($line) {
                    if (preg_match('/^\s*([^\s]+)\s*([^\s].*)\s*$/', $line, $matches)) {
                        $this->addAction(new Action($matches[1], $matches[2]));
                    }
                },
                $lines
            );
        } else {
            $this->getLogger()->error("unrecognized output to fetch revision # from command SVN checkout: {$output}");
            $this->setRevision(-1);
        }

        return $this;
    }
}
