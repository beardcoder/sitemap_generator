<?php
namespace Markussom\SitemapGenerator\Command;

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
use Markussom\SitemapGenerator\Service\GoogleSitemapService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

/**
 * Class TaskCommandController
 */
class TaskCommandController extends CommandController
{
    /**
     * Send a request to google tools api for the sitemap crawling.
     *
     * @param string $xmlSiteUrl http://www.example.com/sitemap.xml
     */
    public function googleSitemapToolCommand($xmlSiteUrl)
    {
        $googleSitemapPing = GeneralUtility::makeInstance(GoogleSitemapService::class, $xmlSiteUrl);
        $httpCode = $googleSitemapPing->sendRequest();

        if ($httpCode === 200) {
            $this->outputLine('success');
        }

        $this->outputLine('error');
    }
}
