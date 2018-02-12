<?php
defined('TYPO3_MODE') or die();

TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Markussom.sitemap_generator',
    'Pi1',
    ['Sitemap' => 'list, googleNewsList']
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = Markussom\SitemapGenerator\Command\TaskCommandController::class;

if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['sitemap_generator'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['sitemap_generator'] = [];
}

if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('realurl')) {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/realurl/class.tx_realurl_autoconfgen.php']['extensionConfiguration']['sitemap_generator'] = \Markussom\SitemapGenerator\Hooks\RealUrlAutoConfiguration::class . '->addConfig';
}
