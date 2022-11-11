<?php
defined('TYPO3') || die();

(static function() {
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
		'ContentConsent',
		'All',
		[
			\T3S\ContentConsent\Controller\ConsentController::class => 'index, ajax'
		],
		// non-cacheable actions
		[
			\T3S\ContentConsent\Controller\ConsentController::class => 'ajax'
		]
	);
})();
