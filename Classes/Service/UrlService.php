<?php
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

namespace Markussom\SitemapGenerator\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class UrlService
 * @package Markussom\SitemapGenerator\Service
 */
class UrlService
{
    /**
     * SitemapRepository constructor.
     * @SuppressWarnings(superglobals)
     */
    public function __construct()
    {
        $this->contentObject = GeneralUtility::makeInstance(ContentObjectRenderer::class);
    }

    /**
     * @param $row
     * @param $pluginConfig
     * @SuppressWarnings(superglobals)
     *
     * @return string
     */
    public function generateUrlFromTypoScript(
        $row,
        $pluginConfig
    ) {
        $url = '';
        $entriesConfiguration = $pluginConfig[1]['urlEntries.'];
        foreach ($entriesConfiguration as $item) {
            if (!empty($item['table'])) {
                $this->contentObject->start($row, $item['table']);
                $url = $this->contentObject->cObjGetSingle($item['url'], $item['url.']);
            }
        }
        return $url;
    }

}
