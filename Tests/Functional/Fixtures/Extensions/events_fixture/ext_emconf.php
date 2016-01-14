<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Events test fixture',
    'description' => 'Simulate events records for functional tests.',
    'category' => 'backend',
    'author' => 'Markus Sommer',
    'author_email' => 'info@creativeworkspace.de',
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
