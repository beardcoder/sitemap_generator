<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'News test fixture',
    'description' => 'Simulate news records for functional tests.',
    'category' => 'backend',
    'author' => 'Nicole Cordes',
    'author_email' => 'typo3@cordes.co',
    'state' => 'stable',
    'version' => '0.1.0',
    'constraints' => [
        'depends' => [
            'typo3' => '7.0.0-7.6.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
