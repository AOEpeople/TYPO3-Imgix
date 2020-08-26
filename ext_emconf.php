<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'imgix',
    'description' => 'Simple integration of imgix in TYPO3',
    'category' => 'system',
    'author' => 'AOE GmbH',
    'author_email' => 'dev@aoe.com',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'author_company' => '',
    'version' => '8.0.1',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.30-8.7.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ]
];
