<?php
declare(strict_types=1);

namespace T3S\ContentConsent\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use Psr\Http\Message\ResponseInterface;

/*
 * This file is part of the TYPO3 extension content_consent.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class ConsentController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

	/**
	 * action index
	 *
	 * @return ResponseInterface
	 */
	public function indexAction(): ResponseInterface
	{
		$lazyLoad = FALSE;
		$contentConsent = FALSE;

		$currentRecord = $this->configurationManager->getContentObject()->data['uid'];

		if ( $this->settings['consent']['cookie'] && isset($_COOKIE['t3scontentconsent_'.$currentRecord]) && $_COOKIE['t3scontentconsent_'.$currentRecord] == 'allow' ) {
			$contentConsent = TRUE;
		} else {

			if ( ExtensionManagementUtility::isLoaded('t3sbootstrap') ) {
				$extconf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');
				$lazyLoad = empty($extconf['lazyLoad']) ? FALSE : TRUE;
			}

			$fileRepository = GeneralUtility::makeInstance(FileRepository::class);
			$thumbnails = $fileRepository->findByRelation('tt_content', 'consentpreviewimage', $currentRecord);
		}

		$assignedValues = [
			'currentRecord' => $currentRecord,
			'contentConsent' => $contentConsent,
			'thumbnail' => $thumbnails[0],
			'lazyLoad' => $lazyLoad
		];
		$this->view->assignMultiple($assignedValues);

		return $this->htmlResponse();
	}



	/**
	 * Displays the selected content with ajax
	 *
	 * @return string
	 */
	public function ajaxAction(): string
	{
		$post = GeneralUtility::_POST();

		if ( !empty($post['cookies'])) {
			$cookieExpire = $this->settings['cookieExpire'] ? (int)$this->settings['cookieExpire'] : 30;
			setcookie('t3scontentconsent_'.(int)$post['currentRecord'], 'allow', time() + (86400 * $cookieExpire), '/');
		}

		$cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class, null);

		$conf ['tables'] = 'tt_content';
		$conf ['source'] = $post['contentByUid'];
		$conf ['dontCheckPid'] = 1;

		return $cObj->cObjGetSingle ('RECORDS', $conf);
	}


}
