<?php
use Markussom\SitemapGenerator\Domain\Model\UrlEntry;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
defined('TYPO3_MODE') or die();

$languageFilePrefix = 'LLL:EXT:sitemap_generator/Resources/Private/Language/locallang_db.xlf:';


$GLOBALS['TCA']['pages'] = array_merge_recursive(
    $GLOBALS['TCA']['pages'],
    [
        'columns' => [
            UrlEntry::EXCLUDE_FROM_SITEMAP => [
                'exclude' => 0,
                'label' => $languageFilePrefix . 'sitemap_generator.tca.pages.exclude_from_sitemap',
                'config' => [
                    'type' => 'check',
                    'items' => [
                        [$languageFilePrefix . 'sitemap_generator.tca.pages.exclude_from_sitemap.label', 1],
                    ],
                ],
            ],
            'sitemap_priority' => [
                'exclude' => 0,
                'label' => $languageFilePrefix . 'sitemap_generator.tca.pages.sitemap_priority',
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectSingle',
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
            'sitemap_changefreq' => [
                'exclude' => 0,
                'label' => $languageFilePrefix . 'sitemap_generator.tca.pages.sitemap_changefreq',
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectSingle',
                    'items' => [
                        [$languageFilePrefix . 'sitemap_generator.tca.pages.please_choose', ''],
                        [$languageFilePrefix . 'sitemap_generator.tca.pages.sitemap_changefreq.always', 'always'],
                        [$languageFilePrefix . 'sitemap_generator.tca.pages.sitemap_changefreq.hourly', 'hourly'],
                        [$languageFilePrefix . 'sitemap_generator.tca.pages.sitemap_changefreq.daily', 'daily'],
                        [$languageFilePrefix . 'sitemap_generator.tca.pages.sitemap_changefreq.weekly', 'weekly'],
                        [$languageFilePrefix . 'sitemap_generator.tca.pages.sitemap_changefreq.monthly', 'monthly'],
                        [$languageFilePrefix . 'sitemap_generator.tca.pages.sitemap_changefreq.yearly', 'yearly'],
                        [$languageFilePrefix . 'sitemap_generator.tca.pages.sitemap_changefreq.never', 'never']
                    ]
                ],
            ],
        ]
    ]
);

ExtensionManagementUtility::addFieldsToPalette(
    'pages',
    'miscellaneous',
    UrlEntry::EXCLUDE_FROM_SITEMAP . ', sitemap_priority, sitemap_changefreq'
);
