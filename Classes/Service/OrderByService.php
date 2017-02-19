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
 * Class OrderByService
 */
class OrderByService
{
    /**
     * Returns the orderBy statement for database connection
     *
     * @param string $orderBy
     * @param string $tablename
     *
     * @return string
     */
    public static function getOrderByString($orderBy, $tablename)
    {
        if (isset($orderBy) && !empty($orderBy)) {
            $cleanOrderByParts = [];
            $tableColumns = $GLOBALS['TCA'][$tablename]['columns'];
            $orderByParts = GeneralUtility::trimExplode(',', $orderBy);
            foreach ($orderByParts as $part) {
                $subparts = GeneralUtility::trimExplode(' ', $part);
                if (count($subparts) === 1) {
                    if (is_array($tableColumns[$subparts[0]])) {
                        $cleanOrderByParts[] = $subparts[0];
                    }
                } elseif (count($subparts) === 2) {
                    if (is_array($tableColumns[$subparts[0]]) && ($subparts[1] === 'ASC' || $subparts[1] === 'DESC')) {
                        $cleanOrderByParts[] = $subparts[0] . ' ' . $subparts[1];
                    }
                }
            }

            return implode(',', $cleanOrderByParts);
        }

        return '';
    }
}
