<?php
namespace Markussom\SitemapGenerator\Domain\Repository;

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
use Markussom\SitemapGenerator\Domain\Model\GoogleNewsUrlEntry;
use Markussom\SitemapGenerator\Domain\Model\Sitemap;
use Markussom\SitemapGenerator\Domain\Model\UrlEntry;
use Markussom\SitemapGenerator\Service\AdditionalWhereService;
use Markussom\SitemapGenerator\Service\FieldValueService;
use TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * Class SitemapRepository
 */
class SitemapRepository
{

    /**
     * @var PageRepository
     */
    protected $pageRepository = null;

    /**
     * @var UriBuilder
     */
    protected $uriBuilder = null;

    /**
     * @var FieldValueService
     */
    protected $fieldValueService = null;

    /**
     * @var TypoScriptParser
     */
    protected $typoScriptParser;

    /**
     * @var array
     */
    protected $pluginConfig = [];

    /**
     * @var string
     */
    protected $pageAdditionalWhere = '';

    /**
     * @var ObjectStorage
     */
    protected $entryStorage = null;

    /**
     * SitemapRepository constructor.
     *
     * @SuppressWarnings(superglobals)
     */
    public function __construct()
    {
        $this->entryStorage = new ObjectStorage();
        $this->typoScriptParser = GeneralUtility::makeInstance(TypoScriptParser::class);
        $this->fieldValueService = new FieldValueService();
        $this->pluginConfig = $this->typoScriptParser->getVal(
            'plugin.tx_sitemapgenerator',
            $GLOBALS['TSFE']->tmpl->setup
        );

        $this->pageAdditionalWhere = AdditionalWhereService::getWhereString(
            $this->pluginConfig['1']['urlEntries.']['pages.']['additionalWhere']
        );
    }

    public function generateSitemap()
    {
        if ($this->findAllEntries()) {
            $sitemap = new Sitemap();
            $sitemap->setEntries($this->entryStorage);
            return $sitemap;
        };
        return null;
    }

    /**
     * Find all pages
     */
    public function findAllPages()
    {
        if (empty($this->pluginConfig['1']['urlEntries.']['pages'])) {
            return;
        }
        $pages = $this->getPages();
        $this->getEntriesFromPages($pages);
    }

    /**
     * Find all entries
     *
     * @return bool
     */
    public function findAllEntries()
    {
        $this->findAllPages();
        $this->generateEntriesFromTypoScript();
        return true;
    }

    /**
     * Generate entries from TypoScript
     *
     * @return array
     */
    public function generateEntriesFromTypoScript()
    {
        $urlEntries = $this->pluginConfig[1]['urlEntries.'];
        $entries = [];
        foreach ($urlEntries as $urlEntry) {
            if (is_array($urlEntry)) {
                $entries = array_merge($entries, $this->mapToEntries($urlEntry));
            }
        }
        return $entries;
    }

    /**
     * Map to entries
     *
     * @param array $typoScriptUrlEntry
     * @SuppressWarnings(superglobals)
     *
     * @return array
     */
    protected function mapToEntries(array $typoScriptUrlEntry)
    {
        if ($typoScriptUrlEntry['table'] && $typoScriptUrlEntry['active'] == 1) {
            $records = $this->getRecordsFromDatabase($typoScriptUrlEntry);
            if ($this->getDatabaseConnection()->sql_num_rows($records)) {
                while ($row = $this->getDatabaseConnection()->sql_fetch_assoc($records)) {
                    $urlEntry = new UrlEntry();
                    $urlEntry->setLoc(
                        $this->fieldValueService->getFieldValue('url', $typoScriptUrlEntry, $row)
                    );
                    if ($typoScriptUrlEntry['lastmod']) {
                        $urlEntry->setLastmod(
                            date('Y-m-d', $this->fieldValueService->getFieldValue('lastmod', $typoScriptUrlEntry, $row))
                        );
                    }
                    if ($typoScriptUrlEntry['changefreq']) {
                        $urlEntry->setChangefreq(
                            $this->fieldValueService->getFieldValue('changefreq', $typoScriptUrlEntry, $row)
                        );
                    }
                    if ($typoScriptUrlEntry['priority']) {
                        $urlEntry->setPriority(
                            number_format(
                                $this->fieldValueService->getFieldValue('priority', $typoScriptUrlEntry, $row) / 10,
                                1,
                                '.',
                                ''
                            )
                        );
                    }
                    $this->entryStorage->attach($urlEntry);
                }
            }
        }
    }

    /**
     * Map to entries
     *
     * @param array $typoScriptUrlEntry
     * @SuppressWarnings(superglobals)
     *
     * @return array
     */
    protected function mapGoogleNewsEntries(array $typoScriptUrlEntry)
    {
        $records = $this->getRecordsFromDatabase($typoScriptUrlEntry);
        $urlEntries = [];
        if ($this->getDatabaseConnection()->sql_num_rows($records)) {
            while ($row = $this->getDatabaseConnection()->sql_fetch_assoc($records)) {
                $urlEntry = new GoogleNewsUrlEntry();
                $urlEntry->setLoc($this->fieldValueService->getFieldValue('url', $typoScriptUrlEntry, $row));
                $urlEntry->setName($row[$typoScriptUrlEntry['name']]);
                $urlEntry->setTitle($row[$typoScriptUrlEntry['title']]);

                if ($typoScriptUrlEntry['language']) {
                    $urlEntry->setLanguage(
                        $this->fieldValueService->getFieldValue('language', $typoScriptUrlEntry, $row)
                    );
                }
                if ($typoScriptUrlEntry['access']) {
                    $urlEntry->setAccess($this->fieldValueService->getFieldValue('access', $typoScriptUrlEntry, $row));
                }
                if ($typoScriptUrlEntry['genres']) {
                    $urlEntry->setGenres($this->fieldValueService->getFieldValue('genres', $typoScriptUrlEntry, $row));
                }
                if ($typoScriptUrlEntry['publicationDate']) {
                    $urlEntry->setPublicationDate(date('Y-m-d', $row[$typoScriptUrlEntry['publicationDate']]));
                }
                if ($typoScriptUrlEntry['keywords']) {
                    $urlEntry->setKeywords($row[$typoScriptUrlEntry['keywords']]);
                }
                if ($typoScriptUrlEntry['stockTickers']) {
                    $urlEntry->setStockTickers($row[$typoScriptUrlEntry['stockTickers']]);
                }
                $urlEntries[] = $urlEntry;
            }
        }
        return $urlEntries;
    }

    /**
     * Get pages from Database
     *
     * @return array
     */
    private function getPages()
    {
        $rootPageId = $this->pluginConfig['1']['urlEntries.']['pages.']['rootPageId'];
        $rootPage = $this->pageRepository->getPage($rootPageId);
        $pages = $this->getSubPagesRecursive($rootPageId);

        return array_merge([$rootPage], $pages);
    }

    /**
     * @param $pages
     */
    public function getEntriesFromPages($pages)
    {
        foreach ($pages as $page) {
            if ($page['doktype'] == 1) {
                $urlEntry = new UrlEntry();
                $uri = $this->uriBuilder->reset()->setTargetPageUid($page['uid'])->setCreateAbsoluteUri(true)->build();
                $urlEntry->setLoc($uri);
                $urlEntry->setLastmod(date('Y-m-d', $page['tstamp']));
                if (isset($page['sitemap_priority'])) {
                    $urlEntry->setPriority(number_format($page['sitemap_priority'] / 10, 1, '.', ''));
                }
                if (isset($page['sitemap_changefreq'])) {
                    $urlEntry->setChangefreq($page['sitemap_changefreq']);
                }
                $this->entryStorage->attach($urlEntry);
            }
        }
    }

    /**
     * Get records from database
     *
     * @param $typoScriptUrlEntry
     * @SuppressWarnings(superglobals)
     *
     * @return bool|\mysqli_result|object
     */
    private function getRecordsFromDatabase($typoScriptUrlEntry)
    {
        if (!isset($GLOBALS['TCA'][$typoScriptUrlEntry['table']])
            || !is_array($GLOBALS['TCA'][$typoScriptUrlEntry['table']]['ctrl'])
        ) {
            return false;
        }

        return $this->getDatabaseConnection()->exec_SELECTquery(
            '*',
            $typoScriptUrlEntry['table'],
            'pid!=0 ' . AdditionalWhereService::getWhereString(
                $typoScriptUrlEntry['additionalWhere']
            ) . $this->pageRepository->enableFields(
                $typoScriptUrlEntry['table']
            )
        );
    }

    /**
     * Get sub pages
     *
     * @param int $startPageId
     * @return array
     */
    private function getSubPages($startPageId)
    {
        return $this->pageRepository->getMenu(
            $startPageId,
            '*',
            'sorting',
            $this->pageRepository->enableFields(
                'pages'
            ) . ' AND ' . UrlEntry::EXCLUDE_FROM_SITEMAP . '!=1' . $this->pageAdditionalWhere
        );
    }

    /**
     * Get sub pages recursive
     *
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

    /**
     * @return bool|\mysqli_result|object
     */
    public function findAllGoogleNewsEntries()
    {
        if (!isset($this->pluginConfig[1]['googleNewsUrlEntry'])
            || !MathUtility::canBeInterpretedAsInteger($this->pluginConfig[1]['googleNewsUrlEntry'])
            || intval($this->pluginConfig[1]['googleNewsUrlEntry']) === 0
        ) {
            return false;
        }

        $entries = $this->mapGoogleNewsEntries($this->pluginConfig[1]['googleNewsUrlEntry.']);
        return $entries;
    }

    /**
     * Returns the database connection
     *
     * @SuppressWarnings(superglobals)
     *
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }

    /**
     * Inject PageRepository
     *
     * @param PageRepository $pageRepository
     */
    public function injectPageRepository(PageRepository $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    /**
     * Inject UriBuilder
     *
     * @param UriBuilder $uriBuilder
     */
    public function injectUriBuilder(UriBuilder $uriBuilder)
    {
        $this->uriBuilder = $uriBuilder;
    }
}
