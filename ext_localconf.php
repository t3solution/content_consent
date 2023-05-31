<?php

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use T3S\ContentConsent\Controller\ConsentController;

defined('TYPO3') || die();

(static function() {
	ExtensionUtility::configurePlugin(
		'ContentConsent',
		'All',
		[ConsentController::class => 'index, ajax'],
		[ConsentController::class => 'ajax']
	);
})();
