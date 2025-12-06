<?php
declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Information\Typo3Version;

defined('TYPO3') || die();


# TYPO3 major version
$majorVersion = GeneralUtility::makeInstance(Typo3Version::class)->getMajorVersion();

if ( $majorVersion == 13 ) {

	$pluginSignature = ExtensionUtility::registerPlugin(
		'content_consent',
		'All',
		'Content consent',
		'contentconsenticon',
		'plugins',
		'Provides a content consent plugin to load any content elements and custom plugins by ajax without jQuery! So you can include Google Maps, YouTube- or Vimeo videos GDPR/DSGVO compliant. Best used with Bootstrap 5',
	);

	ExtensionManagementUtility::addPiFlexFormValue(
		'*',
		'FILE:EXT:content_consent/Configuration/FlexForms/Consent.xml',
		$pluginSignature,
	);

} else {

	// v14
	$pluginSignature = ExtensionUtility::registerPlugin(
		'content_consent',
		'All',
		'Content consent',
		'contentconsenticon',
		'plugins',
		'Provides a content consent plugin to load any content elements and custom plugins by ajax without jQuery! So you can include Google Maps, YouTube- or Vimeo videos GDPR/DSGVO compliant. Best used with Bootstrap 5',
		'FILE:EXT:content_consent/Configuration/FlexForms/Consent.xml'
	);

}


ExtensionManagementUtility::addToAllTCAtypes(
	'tt_content',
	'--div--;Configuration,pi_flexform,',
	$pluginSignature,
	'after:subheader',
);
