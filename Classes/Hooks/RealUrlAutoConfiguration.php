<?php
namespace Markussom\SitemapGenerator\Hooks;

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
/**
 * Inspired by the hook from tx_news
 * 
 */
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * AutoConfiguration-Hook for RealURL
 *
 */
class RealUrlAutoConfiguration
{

    /**
     * Generates additional RealURL configuration and merges it with provided configuration
     *
     * @param       array $params Default configuration
     *
     * @return      array Updated configuration
     */
    public function addConfig($params)
    {
        return array_merge_recursive(
            $params['config'],
            [
                'fileName' => [
                    'index' => [
                        'sitemap.xml' => [
                            'keyValues' => [
                                'type' => 1449874941,
                            ]
                        ]
                    ]
                ]
            ]
        );
    }
}
