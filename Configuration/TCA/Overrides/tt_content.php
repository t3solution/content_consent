<?php
declare(strict_types=1);

use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') || die();


$versionInfo = GeneralUtility::makeInstance(Typo3Version::class);
if ($versionInfo->getMajorVersion() < 13) {

	ExtensionUtility::registerPlugin(
		'ContentConsent',
		'All',
		'Content consent',
	);


} else {

	ExtensionUtility::registerPlugin(
		'ContentConsent',
		'All',
		'Content consent',
		'contentconsenticon',
		'plugins',
		'Provides a content consent plugin to load any content elements and custom plugins by ajax without jQuery! So you can include Google Maps, YouTube- or Vimeo videos GDPR/DSGVO compliant. Best used with Bootstrap 5'
	);

}

$extensionName = GeneralUtility::underscoredToUpperCamelCase('content_consent');
$pluginSignature = strtolower($extensionName) . '_all';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:content_consent/Configuration/FlexForms/Consent.xml');

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['contentconsent_all']='layout,select_key,pages,recursive';
