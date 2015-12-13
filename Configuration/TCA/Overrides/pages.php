<?php
defined('TYPO3_MODE') or die();


$tca = array(
    'columns' => array(
        'exclude_from_sitemap' => array(
            'exclude' => 0,
            'label' => 'Exclude from sitemap',
            'config' => array(
                'type' => 'check',
                'items' => array(
                    array('Exclude', 1),
                ),
            ),
        ),
    )
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('pages', $configuration);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages',
    'miscellaneous',
    'exclude_from_sitemap'
);

\TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TCA']['pages'], $tca);
