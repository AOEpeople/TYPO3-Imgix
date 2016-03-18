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
    'constraints' => array(
        'depends' => array(
            'cms' => '',
            'typo3' => '6.2.0-0.0.0',
            'php' => '5.3.0-0.0.0',
        ),
        'conflicts' => array(),
        'suggests' => array(),
    ),
);
