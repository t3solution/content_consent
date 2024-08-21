<?php
declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die();

ExtensionManagementUtility::addStaticFile('content_consent', 'Configuration/TypoScript', 'Content Consent');
