<?php

$EM_CONF[$_EXTKEY] = array(
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
    'version' => '3.0.1',
    'constraints' => array(
        'depends' => array(
            'typo3' => '8.7.0-8.7.99',
        ),
        'conflicts' => array(),
        'suggests' => array(),
    ),
);
