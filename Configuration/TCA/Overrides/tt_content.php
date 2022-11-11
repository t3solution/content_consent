<?php
defined('TYPO3') || die();

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'ContentConsent',
	'All',
	'Content consent',
	'contentconsenticon'
);

$extensionName = \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase('content_consent');
$pluginSignature = strtolower($extensionName) . '_all';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:content_consent/Configuration/FlexForms/Consent.xml');

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['contentconsent_all']='layout,select_key,pages,recursive,pages';
