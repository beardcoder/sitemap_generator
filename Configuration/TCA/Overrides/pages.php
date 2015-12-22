<?php
defined('TYPO3_MODE') or die();

$languageFilePrefix = 'LLL:EXT:sitemap_generator/Resources/Private/Language/Database.xlf:';

$tca = [
    'columns' => [
        \Markussom\SitemapGenerator\Domain\Model\UrlEntry::EXCLUDE_FROM_SITEMAP => [
            'exclude' => 0,
            'label' => $languageFilePrefix . 'sitemap_generator.tca.pages.exclude_from_sitemap',
            'config' => [
                'type' => 'check',
                'items' => [
                    ['Exclude', 1],
                ],
            ],
        ],
        'sitemap_priority' => [
            'exclude' => 0,
            'label' => $languageFilePrefix . 'sitemap_generator.tca.pages.sitemap_priority',
            'config' => [
                'type' => 'select',
                'items' => [
                    ['0.0', 0],
                    ['0.1', 1],
                    ['0.2', 2],
                    ['0.3', 3],
                    ['0.4', 4],
                    ['0.5', 5],
                    ['0.6', 6],
                    ['0.7', 7],
                    ['0.8', 8],
                    ['0.9', 9],
                    ['1.0', 10],
                ]
            ],
        ],
    ]
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('pages', $configuration);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages',
    'miscellaneous',
    \Markussom\SitemapGenerator\Domain\Model\UrlEntry::EXCLUDE_FROM_SITEMAP . ', sitemap_priority'
);

\TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TCA']['pages'], $tca);
