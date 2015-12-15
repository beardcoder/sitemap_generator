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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

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
     * @var array
     */
    protected $settings = [];

    /**
     * @var \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder
     * @inject
     */
    protected $uriBuilder = null;

    /**
     * SitemapRepository constructor.
     */
    public function __construct()
    {
        $this->configManager = GeneralUtility::makeInstance(ConfigurationManager::class);

        $this->settings = $this->configManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'SitemapGenerator'
        );
    }

    /**
     * @return array
     */
    public function findAllPages()
    {
        $this->generateEntriesFromTypoScript();
        $startPage = $this->pageRepo->getPage($this->settings['urlEntries']['pages']['rootPageId']);
        $pages = $this->pageRepo->getMenu(
            $this->settings['urlEntries']['pages']['rootPageId'],
            '*',
            'sorting',
            $this->pageRepo->enableFields('pages') . 'AND exclude_from_sitemap!=1'
        );
        $pages = array_merge($pages, [$startPage]);
        $urlEntries = [];
        foreach ($pages as $page) {
            if ($page['doktype'] == 1) {
                $urlEntry = new UrlEntry();
                $uri = $this->uriBuilder->reset()->setTargetPageUid($page['uid'])->setCreateAbsoluteUri(true)->build();
                $urlEntry->setLoc($uri);
                $urlEntry->setLastmod(date('Y-m-d', $page['tstamp']));
                $urlEntries[] = $urlEntry;
            }
        }
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
        $urlEntries = $this->settings['urlEntries'];
        $entries = [];
        foreach ($urlEntries as $urlEntry) {
            $entries = $this->mapToEntries($urlEntry);
        }
        return $entries;

    }

    /**
     * @param array $typoScriptUrlEntry
     * @return array
     */
    protected function mapToEntries(array $typoScriptUrlEntry)
    {
        if ($typoScriptUrlEntry['table']) {
            $result = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
                '*',
                $typoScriptUrlEntry['table'],
                'pid!=0' . $typoScriptUrlEntry['additionalWhere'] . $this->pageRepo->enableFields(
                    $typoScriptUrlEntry['table']
                )
            );
            $urlEntries = [];
            if ($GLOBALS['TYPO3_DB']->sql_num_rows($result)) {
                while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
                    $urlEntry = new UrlEntry();
                    $arguments = $typoScriptUrlEntry['url']['arguments'];
                    $this->replaceRecursiveFieldData($arguments, $row);
                    $uri = $this->uriBuilder
                        ->reset()
                        ->setTargetPageUid($typoScriptUrlEntry['url']['pid'])
                        ->setArguments($arguments)
                        ->setCreateAbsoluteUri(true)
                        ->build();
                    $urlEntry->setLoc($uri);
                    if ($typoScriptUrlEntry['lastmod']) {
                        $urlEntry->setLastmod(date('Y-m-d', $row[$typoScriptUrlEntry['lastmod']]));
                    }
                    $urlEntries[] = $urlEntry;
                }
            }
            return $urlEntries;
        }
        return [];

    }

    /**
     * @param $arguments
     * @param $row
     */
    public function replaceRecursiveFieldData(
        &$arguments,
        $row
    ) {
        foreach ($arguments as $key => $argument) {
            if (is_array($argument)) {
                $this->replaceRecursiveFieldData($arguments[$key], $row);
            } else {
                if (strstr($argument, '{field:')) {
                    $field = str_replace('{field:', '', $argument);
                    $field = str_replace('}', '', $field);
                    $arguments[$key] = $row[$field];
                }
            }
        }
    }
}
