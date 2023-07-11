<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Content Consent',
    'description' => 'Provides a content consent plugin to load any content elements and custom plugins by ajax without jQuery! So you can include Google Maps, YouTube- or Vimeo videos GDPR/DSGVO compliant. Best used with Bootstrap 5',
    'category' => 'plugin',
    'author' => 'Helmut Hackbarth',
    'author_email' => 'typo3@t3solution.de',
    'state' => 'stable',
    'clearCacheOnLoad' => 0,
    'version' => '2.0.1',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-12.9.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
