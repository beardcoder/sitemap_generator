<?php
defined('TYPO3_MODE') or die();

use Markussom\SitemapGenerator\Command\TaskCommandController;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

ExtensionUtility::configurePlugin(
    'Markussom.sitemap_generator',
    'Pi1',
    ['Sitemap' => 'list, googleNewsList']
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = TaskCommandController::class;
