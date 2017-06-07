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
use Markussom\SitemapGenerator\Service\LimitService;
use Markussom\SitemapGenerator\Service\OrderByService;
use Markussom\SitemapGenerator\Service\PageUrlService;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * Class SitemapRepository
 */
class SitemapRepository
{
    /**
     * @var FieldValueService
     */
    protected $fieldValueService = null;

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
     * @var PageRepository
     */
    protected $pageRepository = null;

    /**
     * @var TypoScriptParser
     */
    protected $typoScriptParser = null;

    protected $cacheInstance;

    /**
     * SitemapRepository constructor.
     *
     * @SuppressWarnings(superglobals)
     */
    public function __construct()
    {
        $this->makeClassInstance();
        $this->cacheInstance = GeneralUtility::makeInstance(CacheManager::class)->getCache('sitemap_generator');

        $this->pluginConfig = $this->typoScriptParser->getVal(
            'plugin.tx_sitemapgenerator',
            $GLOBALS['TSFE']->tmpl->setup
        );

        $this->pageAdditionalWhere = AdditionalWhereService::getWhereString(
            $this->pluginConfig['1']['urlEntries.']['pages.']['additionalWhere']
        );
    }

    /**
     * @return object
     */
    protected function makeClassInstance()
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->entryStorage = $objectManager->get(ObjectStorage::class);
        $this->pageRepository = $objectManager->get(PageRepository::class);
        $this->fieldValueService = $objectManager->get(FieldValueService::class);
        $this->typoScriptParser = $objectManager->get(TypoScriptParser::class);
    }

    /**
     * Generate a sitemap
     *
     * @return Sitemap|null
     */
    public function generateSitemap()
    {
        if ($this->findAllEntries()) {
            $sitemap = GeneralUtility::makeInstance(Sitemap::class);
            $sitemap->setUrlEntries($this->entryStorage);

            return $sitemap;
        }

        return null;
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
     * Find all pages
     */
    public function findAllPages()
    {
        if (empty($this->pluginConfig['1']['urlEntries.']['pages'])) {
            return;
        }
        $pages = $this->hidePagesIfNotTranslated($this->getPages());

        $this->getEntriesFromPages($pages);
    }

    /**
     * Remove page if not translated
     *
     * @param array $pages
     *
     * @return array
     */
    private function hidePagesIfNotTranslated($pages)
    {
        $language = GeneralUtility::_GET('L');
        if ($this->isPageNotTranslated($language)) {
            foreach ($pages as $key => $page) {
                $pageOverlay = $this->pageRepository->getPageOverlay($page, $language);
                if (empty($pageOverlay['_PAGES_OVERLAY'])) {
                    unset($pages[$key]);
                }
            }
        }

        return $pages;
    }

    /**
     * @param $language
     *
     * @return bool
     */
    private function isPageNotTranslated($language)
    {
        $ifNotTranslated = $this->pluginConfig['1']['urlEntries.']['pages.']['hidePagesIfNotTranslated'];

        return intval($language) !== 0 && intval($ifNotTranslated) === 1;
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

        $cacheIdentifier = md5($rootPageId . '-pagesForSitemap');
        if ($this->cacheInstance->has($cacheIdentifier)) {
            $pages = $this->cacheInstance->get($cacheIdentifier);
        } else {
            $pages = $this->getSubPagesRecursive($rootPageId);
            $this->cacheInstance->set($cacheIdentifier, $pages, ['pagesForSitemap']);
        }

        return array_merge([$rootPage], $pages);
    }

    /**
     * Get sub pages recursive
     *
     * @param $rootPageId
     *
     * @return array
     */
    private function getSubPagesRecursive($rootPageId)
    {
        $pages = $this->getSubPages($rootPageId);
        foreach ($pages as $page) {
            if (false === $this->isPageTreeLeaf($page)) {
                ArrayUtility::mergeRecursiveWithOverrule(
                    $pages,
                    $this->getSubPagesRecursive($page['uid'])
                );
            }
        }

        return $pages;
    }

    /**
     * Get sub pages
     *
     * @param int $startPageId
     *
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
     * @param $pages
     */
    public function getEntriesFromPages($pages)
    {
        foreach ($pages as $page) {
            if ($this->hasPageAnAllowedDoktype($page)) {
                $urlEntry = GeneralUtility::makeInstance(UrlEntry::class);
                $uri = PageUrlService::generatePageUrl($page['uid']);
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
     * @param $page
     *
     * @return bool
     */
    private function hasPageAnAllowedDoktype($page)
    {
        return GeneralUtility::inList(
            $this->pluginConfig['1']['urlEntries.']['pages.']['allowedDoktypes'],
            $page['doktype']
        );
    }

    /**
     * Determines if the child page tree should not be fetched based on the current page.
     * This is for example a "Backend User Section" or "Recycler" (configurable) or the
     * page has "Stop Page Tree" activated (cannot be deactivated).
     *
     * A leaf is the last element in a tree.
     *
     * @param array $page
     * @return bool
     */
    private function isPageTreeLeaf(array $page)
    {
        if ('1' === $page['php_tree_stop']) {
            return true;
        }

        return GeneralUtility::inList(
            $this->pluginConfig['1']['urlEntries.']['pages.']['stopPageTreeDoktypes'],
            $page['doktype']
        );
    }

    /**
     * Generate entries from TypoScript
     */
    public function generateEntriesFromTypoScript()
    {
        $urlEntries = $this->pluginConfig[1]['urlEntries.'];
        foreach ($urlEntries as $urlEntry) {
            if (!empty($urlEntry['active'])) {
                $this->mapToEntries($urlEntry);
            }
        }
    }

    /**
     * Map to entries
     *
     * @param array $typoScriptUrlEntry
     * @SuppressWarnings(superglobals)
     *
     */
    protected function mapToEntries(array $typoScriptUrlEntry)
    {
        if ($typoScriptUrlEntry['table'] && $typoScriptUrlEntry['active'] == 1) {
            $records = $this->getRecordsFromDatabase($typoScriptUrlEntry);
            if ($this->getDatabaseConnection()->sql_num_rows($records)) {
                while ($row = $this->getDatabaseConnection()->sql_fetch_assoc($records)) {
                    $row = $this->hideRecordIfNotTranslated($typoScriptUrlEntry, $row);
                    if (!empty($row)) {
                        /** @var UrlEntry $urlEntry */
                        $urlEntry = GeneralUtility::makeInstance(UrlEntry::class);
                        $urlEntry->setLoc(
                            $this->fieldValueService->getFieldValue('url', $typoScriptUrlEntry, $row)
                        );
                        if (isset($typoScriptUrlEntry['lastmod'])) {
                            $urlEntry->setLastmod(
                                date(
                                    'Y-m-d',
                                    $this->fieldValueService->getFieldValue('lastmod', $typoScriptUrlEntry, $row)
                                )
                            );
                        }
                        if (isset($typoScriptUrlEntry['changefreq'])) {
                            $urlEntry->setChangefreq(
                                $this->fieldValueService->getFieldValue('changefreq', $typoScriptUrlEntry, $row)
                            );
                        }
                        if (isset($typoScriptUrlEntry['priority'])) {
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

        $language = '';
        if (intval($typoScriptUrlEntry['hideIfNotTranslated']) === 1) {
            $language = 'AND (sys_language_uid=\'-1\' OR sys_language_uid="' . intval(GeneralUtility::_GET('L')) . '") ';
        }

        return $this->getDatabaseConnection()->exec_SELECTquery(
            '*',
            $typoScriptUrlEntry['table'],
            'pid!=0 ' . $language . ' ' . AdditionalWhereService::getWhereString(
                $typoScriptUrlEntry['additionalWhere']
            ) . $this->pageRepository->enableFields(
                $typoScriptUrlEntry['table']
            ),
            '',
            OrderByService::getOrderByString(
                $typoScriptUrlEntry['orderBy'],
                $typoScriptUrlEntry['table']
            ),
            LimitService::getLimitString(
                $typoScriptUrlEntry['limit']
            )
        );
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
     * @param $recordConfig
     * @param $record
     *
     * @return mixed
     */
    private function hideRecordIfNotTranslated($recordConfig, $record)
    {
        $language = GeneralUtility::_GET('L');
        if ($this->isRecordNotTranslated($recordConfig, $record, $language)) {
            $record = $this->pageRepository->getRecordOverlay($recordConfig['table'], $record, $language);
            if (intval($record['l10n_parent']) !== 0) {
                return $record;
            }

            return null;
        }

        return $record;
    }

    /**
     * @param $recordConfig
     * @param $record
     * @param $language
     *
     * @return bool
     */
    private function isRecordNotTranslated($recordConfig, $record, $language)
    {
        return $record['sys_language_uid'] !== '-1' && intval($language) !== 0 && intval($recordConfig['hideIfNotTranslated']) === 1;
    }

    /**
     * @return bool|\mysqli_result|object|array
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
                $row = $this->hideRecordIfNotTranslated($typoScriptUrlEntry, $row);

                $urlEntry = GeneralUtility::makeInstance(GoogleNewsUrlEntry::class);
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
}
