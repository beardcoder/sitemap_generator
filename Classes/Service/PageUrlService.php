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
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class GoogleSitemapService
 */
class PageUrlService
{

    /**
     * Generates the current page's URL.
     *
     * Uses the provided GET parameters, page id and language id.
     *
     * @param int $uid
     *
     * @return string URL of the current page.
     */
    public static function generatePageUrl(int $uid): string
    {
        $contentObject = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        $conf = [
            'parameter' => (int) $uid,
            'linkAccessRestrictedPages' => '1',
            'useCacheHash' => 1,
            'returnLast ' => 'url',
            'forceAbsoluteUrl' => 1
        ];
        $language = GeneralUtility::_GET('L');
        if (!empty($language)) {
            $conf['additionalParams'] = '&L=' . $language;
        }
        $url = $contentObject->typoLink_URL($conf);

        // clean up
        if ($url == '') {
            $url = '/';
        }

        return $url;
    }
}
