<?php
defined('TYPO3_MODE') or die();

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Markussom.sitemap_generator',
    'Pi1',
    ['Sitemap' => 'list, googleNewsList']
);
