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
 * Class GoogleSitemapService
 */
class GoogleSitemapService
{
    /**
     * The URL of the sitemap to add. For example: http://www.example.com/sitemap.xml
     *
     * @var string
     */
    protected $xmlSiteUrl = '';

    /**
     * Url to google Api
     *
     * @var string
     */
    protected $toolUrl = 'https://www.google.com/webmasters/tools/ping?sitemap=';

    /**
     * GoogleSitemapService constructor.
     * @param string $xmlSiteUrl
     */
    public function __construct($xmlSiteUrl)
    {
        $this->xmlSiteUrl = $xmlSiteUrl;
    }

    /**
     * Send the request to google
     *
     * @return int
     */
    public function sendRequest()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getGoogleSitemapToolUrl());
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode;
    }

    /**
     * Generate Google tool url for sitemap submit
     */
    protected function getGoogleSitemapToolUrl()
    {
        $url = $this->toolUrl . urlencode($this->xmlSiteUrl);
        if (!GeneralUtility::isValidUrl($url)) {
            return null;
        }
        return $url;
    }
}
