<?php
namespace Markussom\SitemapGenerator\Domain\Repository;

use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use Exception;
use mysqli_result;
use TYPO3\CMS\Core\Database\DatabaseConnection;
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
     * @var FrontendInterface
     */
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

        $this->pluginConfig = GeneralUtility::removeDotsFromTS(
            $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_sitemapgenerator.']
        );

        $this->pageAdditionalWhere = AdditionalWhereService::getWhereString(
            $this->pluginConfig['urlEntries']['pages']['additionalWhere']
        );
    }

    /**
     * Make instance of needed classes
     */
    protected function makeClassInstance(): void
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->entryStorage = $objectManager->get(ObjectStorage::class);
        $this->pageRepository = $objectManager->get(PageRepository::class);
        $this->fieldValueService = $objectManager->get(FieldValueService::class);
    }

    /**
     * Generate a sitemap
     *
     * @return Sitemap|null
     */
    public function generateSitemap(): ?object
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
    public function findAllEntries(): bool
    {
        $this->findAllPages();
        $this->generateEntriesFromTypoScript();

        return true;
    }

    /**
     * Find all pages
     */
    public function findAllPages(): void
    {
        if (empty($this->pluginConfig['urlEntries']['pages'])) {
            return;
        }
        $pages = $this->hidePagesIfNotTranslated($this->getPages());
        $pages = $this->hidePagesIfHiddenInDefaultTranslation($pages);

        $this->getEntriesFromPages($pages);
    }

    /**
     * Remove page if not translated
     *
     * @param array $pages
     *
     * @return array
     */
    private function hidePagesIfNotTranslated(array $pages): array
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
    private function isPageNotTranslated($language): bool
    {
        $ifNotTranslated = $this->pluginConfig['urlEntries']['pages']['hidePagesIfNotTranslated'];

        return (int)$language !== 0 && (int)$ifNotTranslated === 1;
    }

    /**
     * Remove page if hidden in default translation
     *
     * @param array $pages
     *
     * @return array
     */
    private function hidePagesIfHiddenInDefaultTranslation(array $pages): array
    {
        $language = GeneralUtility::_GET('L');

        if ($language != 0) {
            return $pages;
        }

        foreach ($pages as $key => $page) {
            if ($page['l18n_cfg'] === 1) {
                unset($pages[$key]);
            }
        }

        return $pages;
    }

    /**
     * Get pages from Database
     *
     * @return array
     */
    private function getPages(): array
    {
        $rootPageId = $this->pluginConfig['urlEntries']['pages']['rootPageId'];
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
    private function getSubPagesRecursive($rootPageId): array
    {
        $pages = $this->getSubPages($rootPageId);
        foreach ($pages as $page) {
            if (!$this->isPageTreeLeaf($page)) {
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
     * @return array
     */
    private function getSubPages(int $startPageId): array
    {
        $where = $this->pageRepository->enableFields('pages')
            . ' AND ' . UrlEntry::EXCLUDE_FROM_SITEMAP . '!=1' . $this->pageAdditionalWhere;
        try {
            return $this->pageRepository->getMenu($startPageId, '*', 'sorting', $where);
        } catch (Exception $exception) {
            return [];
        }
    }

    /**
     * @param $pages
     */
    public function getEntriesFromPages($pages): void
    {
        foreach ($pages as $page) {
            if ($this->hasPageAnAllowedDoktype($page)) {
                $urlEntry = GeneralUtility::makeInstance(UrlEntry::class);
                $uri = PageUrlService::generatePageUrl($page['uid']);
                $urlEntry->setLoc($uri);
                $urlEntry->setLastmod(date('Y-m-d', $page['SYS_LASTCHANGED']));
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
    private function hasPageAnAllowedDoktype($page): bool
    {
        return GeneralUtility::inList(
            $this->pluginConfig['urlEntries']['pages']['allowedDoktypes'],
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
     *
     * @return bool
     */
    private function isPageTreeLeaf(array $page): bool
    {
        if ('1' === $page['php_tree_stop']) {
            return true;
        }

        return GeneralUtility::inList(
            $this->pluginConfig['urlEntries']['pages']['stopPageTreeDoktypes'],
            $page['doktype']
        );
    }

    /**
     * Generate entries from TypoScript
     */
    public function generateEntriesFromTypoScript(): void
    {
        $urlEntries = $this->pluginConfig['urlEntries'];
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
     */
    protected function mapToEntries(array $typoScriptUrlEntry): void
    {
        if ($typoScriptUrlEntry['table'] && $typoScriptUrlEntry['active'] == 1) {
            $records = $this->getRecordsFromDatabase($typoScriptUrlEntry);
            if ($this->getDatabaseConnection()->sql_num_rows($records) !== 0) {
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
     * @return bool|object
     */
    private function getRecordsFromDatabase($typoScriptUrlEntry)
    {
        if (!isset($GLOBALS['TCA'][$typoScriptUrlEntry['table']])
            || !is_array($GLOBALS['TCA'][$typoScriptUrlEntry['table']]['ctrl'])
        ) {
            return false;
        }

        $language = '';
        if ((int)$typoScriptUrlEntry['hideIfNotTranslated'] === 1) {
            $language = 'AND (sys_language_uid=\'-1\' OR sys_language_uid="' . (int)GeneralUtility::_GET('L') . '") ';
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
     * @return DatabaseConnection
     */
    protected function getDatabaseConnection(): DatabaseConnection
    {
        return $GLOBALS['TYPO3_DB'];
    }

    /**
     * @param $recordConfig
     * @param $record
     *
     * @return mixed|null
     */
    private function hideRecordIfNotTranslated($recordConfig, $record)
    {
        $language = GeneralUtility::_GET('L');
        if ($this->isRecordNotTranslated($recordConfig, $record, $language)) {
            $record = $this->pageRepository->getRecordOverlay($recordConfig['table'], $record, $language);
            if ((int)$record['l10n_parent'] !== 0) {
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
    private function isRecordNotTranslated($recordConfig, $record, $language): bool
    {
        return $record['sys_language_uid'] !== '-1' && (int)$language !== 0 && (int)$recordConfig['hideIfNotTranslated'] === 1;
    }

    /**
     * @return bool|object[]
     */
    public function findAllGoogleNewsEntries()
    {
        if (!isset($this->pluginConfig['googleNewsUrlEntry'])
            || !MathUtility::canBeInterpretedAsInteger($this->pluginConfig['googleNewsUrlEntry'])
            || (int)$this->pluginConfig['googleNewsUrlEntry'] === 0
        ) {
            return false;
        }

        return $this->mapGoogleNewsEntries($this->pluginConfig['googleNewsUrlEntry']);
    }

    /**
     * Map to entries
     *
     * @param array $typoScriptUrlEntry
     * @SuppressWarnings(superglobals)
     *
     * @return object[]
     */
    protected function mapGoogleNewsEntries(array $typoScriptUrlEntry): array
    {
        $records = $this->getRecordsFromDatabase($typoScriptUrlEntry);
        $urlEntries = [];
        if ($this->getDatabaseConnection()->sql_num_rows($records) !== 0) {
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
