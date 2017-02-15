<?php
namespace Markussom\SitemapGenerator\Service;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class LimitService
 */
class LimitService
{
    /**
     * Returns the limit statement for database connection
     *
     * @param string $limit
     * @return string
     */
    public static function getLimitString($limit)
    {
        if (isset($limit) && !empty($limit)) {
            $limitParts = GeneralUtility::trimExplode(',', $limit);
            if (count($limitParts) === 1) {
                return intval($limitParts[0]);
            } elseif (count($limitParts) === 2) {
                return intval($limitParts[0]) . ',' . intval($limitParts[1]);
            }
        }
        return '';
    }
}
