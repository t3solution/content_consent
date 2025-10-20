<?php
declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') || die();

$pluginSignature = ExtensionUtility::registerPlugin(
	'content_consent',
	'All',
	'Content consent',
	'contentconsenticon',
	'plugins',
	'Provides a content consent plugin to load any content elements and custom plugins by ajax without jQuery! So you can include Google Maps, YouTube- or Vimeo videos GDPR/DSGVO compliant. Best used with Bootstrap 5',
);

ExtensionManagementUtility::addToAllTCAtypes(
	'tt_content',
	'--div--;Configuration,pi_flexform,',
	$pluginSignature,
	'after:subheader',
);

ExtensionManagementUtility::addPiFlexFormValue(
	'*',
	'FILE:EXT:content_consent/Configuration/FlexForms/Consent.xml',
	$pluginSignature,
);
