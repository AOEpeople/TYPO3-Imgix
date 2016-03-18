<?php

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Aoe.' . $_EXTKEY,
    'Loader',
    array(
        'Load' => 'initial',
    ),
    array()
);
