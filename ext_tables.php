<?php

defined('TYPO3') || die;

(static function () {
    // Extend user settings
    $GLOBALS['TYPO3_USER_SETTINGS']['columns']['hideYoastInPageModule'] = [
        'label' => 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:usersettings.hideYoastInPageModule',
        'type' => 'check'
    ];
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToUserSettings(
        '--div--;LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:usersettings.title,hideYoastInPageModule'
    );

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess'][]
        = \YoastSeoForTypo3\YoastSeo\Hooks\BackendYoastConfig::class . '->renderConfig';
})();
