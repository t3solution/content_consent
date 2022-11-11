<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Content Consent',
    'description' => 'Provides a content consent plugin to load any content elements and custom plugins by ajax without jQuery! Best used with Bootstrap 5',
    'category' => 'plugin',
    'author' => 'Helmut Hackbarth',
    'author_email' => 'typo3@t3solution.de',
    'state' => 'stable',
    'clearCacheOnLoad' => 0,
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
