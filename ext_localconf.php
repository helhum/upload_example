<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Helhum.' . $_EXTKEY,
    'Piexample',
    [
        'Example' => 'list, show, new, create, edit, update, delete',
    ],
    // non-cacheable actions
    [
        'Example' => 'list, show, new, create, edit, update, delete',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerTypeConverter('Helhum\\UploadExample\\Property\\TypeConverter\\UploadedFileReferenceConverter');
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerTypeConverter('Helhum\\UploadExample\\Property\\TypeConverter\\ObjectStorageConverter');
