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
    'version' => '11.0.6',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ]
];
