<?php
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use Markussom\SitemapGenerator\Command\TaskCommandController;
defined('TYPO3_MODE') or die();

ExtensionUtility::configurePlugin(
    'Markussom.sitemap_generator',
    'Pi1',
    ['Sitemap' => 'list, googleNewsList']
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = TaskCommandController::class;

if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['sitemap_generator'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['sitemap_generator'] = [];
}
