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

namespace Markussom\SitemapGenerator\Domain\Repository;

use Markussom\SitemapGenerator\Domain\Model\UrlEntry;
use TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;

/**
 * Class SitemapRepository
 * @package Markussom\SitemapGenerator\Domain\Repository
 */
class SitemapRepository
{

    /**
     * @var \TYPO3\CMS\Frontend\Page\PageRepository
     * @inject
     */
    protected $pageRepo = null;

    /**
     * @var ConfigurationManager
     */
    protected $configManager = null;

    /**
     * @var \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder
     * @inject
     */
    protected $uriBuilder = null;

    /**
     * @var \Markussom\SitemapGenerator\Service\UrlService
     * @inject
     */
    protected $urlService = null;

    /**
     * @var TypoScriptParser
     */
    protected $typoScriptParser;

    /**
     * @var array
     */
    protected $pluginConfig = [];

    /**
     * SitemapRepository constructor.
     * @SuppressWarnings(superglobals)
     */
    public function __construct()
    {
        $this->typoScriptParser = GeneralUtility::makeInstance(TypoScriptParser::class);

        $this->pluginConfig = $this->typoScriptParser->getVal(
            'plugin.tx_sitemapgenerator',
            $GLOBALS['TSFE']->tmpl->setup
        );
    }

    /**
     * @return array
     */
    public function findAllPages()
    {
        if (empty($this->pluginConfig['1']['urlEntries.']['pages'])) {
            return [];
        }
        $pages = $this->getPages();
        $urlEntries = $this->getEntriesFromPages($pages);

        return $urlEntries;
    }

    /**
     * @return array
     */
    public function findAllEntries()
    {
        $entries = $this->findAllPages();
        $typoScript = $this->generateEntriesFromTypoScript();
        return array_merge($entries, $typoScript);
    }

    /**
     * @return array
     */
    public function generateEntriesFromTypoScript()
    {
        $urlEntries = $this->pluginConfig[1]['urlEntries.'];
        $entries = [];
        foreach ($urlEntries as $urlEntry) {
            if (is_array($urlEntry)) {
                $entries = $this->mapToEntries($urlEntry);
            }
        }
        return $entries;

    }

    /**
     * @param array $typoScriptUrlEntry
     * @SuppressWarnings(superglobals)
     * @return array
     */
    protected function mapToEntries(array $typoScriptUrlEntry)
    {
        if ($typoScriptUrlEntry['table'] && $typoScriptUrlEntry['active'] == 1) {

            $records = $this->getRecordsFromDatabase($typoScriptUrlEntry);
            $urlEntries = [];
            if ($this->getDatabaseConnection()->sql_num_rows($records)) {
                while ($row = $this->getDatabaseConnection()->sql_fetch_assoc($records)) {
                    $urlEntry = new UrlEntry();
                    $urlEntry->setLoc($this->urlService->generateUrlFromTypoScript($row, $this->pluginConfig));
                    if ($typoScriptUrlEntry['lastmod']) {
                        $urlEntry->setLastmod(date('Y-m-d', $row[$typoScriptUrlEntry['lastmod']]));
                    }
                    if ($typoScriptUrlEntry['changefreq']) {
                        $urlEntry->setChangefreq($row[$typoScriptUrlEntry['changefreq']]);
                    }
                    if ($typoScriptUrlEntry['priority']) {
                        $urlEntry->setPriority(sprintf('%01.1f', $row[$typoScriptUrlEntry['priority']] / 10));
                    }
                    $urlEntries[] = $urlEntry;
                }
            }
            return $urlEntries;
        }
        return [];

    }

    /**
     * Get pages from Database
     *
     * @return array
     */
    private function getPages()
    {
        $rootPageId = $this->pluginConfig['1']['urlEntries.']['pages.']['rootPageId'];
        $rootPage = $this->pageRepo->getPage($rootPageId);
        $pages = $this->getSubPagesRecursive($rootPageId);

        return array_merge([$rootPage], $pages);
    }

    /**
     * @param $pages
     * @return array
     */
    public function getEntriesFromPages($pages)
    {
        $urlEntries = '';
        foreach ($pages as $page) {
            if ($page['doktype'] == 1) {
                $urlEntry = new UrlEntry();
                $uri = $this->uriBuilder->reset()->setTargetPageUid($page['uid'])->setCreateAbsoluteUri(true)->build();
                $urlEntry->setLoc($uri);
                $urlEntry->setLastmod(date('Y-m-d', $page['tstamp']));
                if (isset($page['sitemap_priority'])) {
                    $urlEntry->setPriority(sprintf('%01.1f', $page['sitemap_priority'] / 10));
                }
                if (isset($page['sitemap_changefreq'])) {
                    $urlEntry->setChangefreq($page['sitemap_changefreq']);
                }
                $urlEntries[] = $urlEntry;
            }
        }
        return $urlEntries;
    }

    /**
     * Returns the database connection
     *
     * @SuppressWarnings(superglobals)
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }

    /**
     * Get records from database
     *
     * @param $typoScriptUrlEntry
     * @return bool|\mysqli_result|object
     */
    private function getRecordsFromDatabase($typoScriptUrlEntry)
    {
        if (
            !isset($GLOBALS['TCA'][$typoScriptUrlEntry['table']])
            || !is_array($GLOBALS['TCA'][$typoScriptUrlEntry['table']]['ctrl'])
        ) {
            return false;
        }

        return $this->getDatabaseConnection()->exec_SELECTquery(
            '*',
            $typoScriptUrlEntry['table'],
            'pid!=0' . $typoScriptUrlEntry['additionalWhere'] . $this->pageRepo->enableFields(
                $typoScriptUrlEntry['table']
            )
        );
    }

    /**
     * @param int $startPageId
     * @return array
     */
    private function getSubPages($startPageId)
    {
        return $this->pageRepo->getMenu(
            $startPageId,
            '*',
            'sorting',
            $this->pageRepo->enableFields('pages') . 'AND ' . UrlEntry::EXCLUDE_FROM_SITEMAP . '!=1'
        );
    }

    /**
     * @param $rootPageId
     * @return array
     */
    private function getSubPagesRecursive($rootPageId)
    {
        $pages = $this->getSubPages($rootPageId);
        foreach ($pages as $page) {
            ArrayUtility::mergeRecursiveWithOverrule(
                $pages,
                $this->getSubPagesRecursive($page['uid'])
            );
        }
        return $pages;
    }
}
