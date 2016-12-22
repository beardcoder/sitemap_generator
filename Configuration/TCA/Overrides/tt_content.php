<?php
defined('TYPO3_MODE') or die();

TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'sitemap_generator',
    'Configuration/TypoScript',
    'Sitemap Generator'
);

