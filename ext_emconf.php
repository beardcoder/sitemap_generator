<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Sitemap Generator',
    'description' => 'Easy to use sitemap generator for pages and records.',
    'category' => 'plugin',
    'author' => 'Markus Sommer',
    'author_email' => 'markussom@posteo.de',
    'state' => 'beta',
    'version' => '0.8.1',
    'constraints' => [
        'depends' => [
            'typo3' => '6.2.7-8.99.99',
            'php' => '5.5.0-0.0.0',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
