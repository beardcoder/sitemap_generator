<?php
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
defined('TYPO3_MODE') or die();

ExtensionManagementUtility::addStaticFile(
    'sitemap_generator',
    'Configuration/TypoScript',
    'Sitemap Generator'
);
