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
     *
     * @param string $xmlSiteUrl
     */
    public function __construct(string $xmlSiteUrl)
    {
        $this->xmlSiteUrl = $xmlSiteUrl;
    }

    /**
     * Send the request to google
     *
     * @return int
     */
    public function sendRequest(): int
    {
        $curlInit = curl_init();
        curl_setopt($curlInit, CURLOPT_URL, $this->getGoogleSitemapToolUrl());
        curl_setopt($curlInit, CURLOPT_HEADER, true);
        curl_setopt($curlInit, CURLOPT_NOBODY, true);
        curl_setopt($curlInit, CURLOPT_RETURNTRANSFER, true);
        curl_exec($curlInit);
        $httpCode = curl_getinfo($curlInit, CURLINFO_HTTP_CODE);
        curl_close($curlInit);

        return $httpCode;
    }

    /**
     * Generate Google tool url for sitemap submit
     */
    protected function getGoogleSitemapToolUrl(): ?string
    {
        $url = $this->toolUrl . urlencode($this->xmlSiteUrl);
        if (!GeneralUtility::isValidUrl($url)) {
            return null;
        }

        return $url;
    }
}
