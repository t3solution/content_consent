<?php
declare(strict_types=1);

namespace T3S\ContentConsent\Controller;

use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use Psr\Http\Message\ResponseInterface;

/*
 * This file is part of the TYPO3 extension content_consent.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class ConsentController extends ActionController
{

	/**
	 * action index
	 *
	 * @return ResponseInterface
	 */
	public function indexAction(): ResponseInterface
	{
		$contentConsent = FALSE;
		$customThumbnail = [];
		$defaultThumbnail = [];
		$properties = [];
		$extension = '';
		$type = 0;
		$currentRecord = $this->request->getAttribute('currentContentObject')->data['uid'];

		if ( $this->settings['consent']['cookie'] 
		 && isset($_COOKIE['t3scontentconsent_'.$currentRecord])
		 && $_COOKIE['t3scontentconsent_'.$currentRecord] == 'allow' ) {

			$contentConsent = TRUE;

		} else {

			$fileRepository = GeneralUtility::makeInstance(FileRepository::class);

			// Custom thumbnail
			if ( !empty($this->settings['consent']['thumbnail']) ) {

				$relatedCustomThumbnails = $fileRepository->findByRelation('tt_content', 'settings.consent.thumbnail', $currentRecord);
				$customThumbnail = !empty($relatedCustomThumbnails[0]) ? $relatedCustomThumbnails[0] : [];
				$defaultThumbnail = [];
				if ( empty($customThumbnail) ) {
					// if media is hidden -> use default
					$defaultThumbnail = $fileRepository->findByRelation('tt_content', 'assets', (int) $this->settings['consent']['contentByUid'])[0];
				}
			}

			// Default thumbnail
			if ( !empty($this->settings['consent']['defaultThumbnail']) ) {

				$relatedDefaultThumbnails = $fileRepository->findByRelation('tt_content', 'assets', (int) $this->settings['consent']['contentByUid']);
				if ( !empty($relatedDefaultThumbnails[0]) ) {
					$defaultThumbnail = $relatedDefaultThumbnails[0];
					$properties = $defaultThumbnail->getOriginalFile()->getProperties();
					$extension = $properties['extension'];
					$type = $properties['type'];
				}

			} else {

				$defaultThumbnail = [];
			}
		}

		$assignedValues = [
			'currentRecord' => $currentRecord,
			'contentConsent' => $contentConsent,
			'extension' => $extension,
			'type' => $type,
			'customThumbnail' => $customThumbnail,
			'defaultThumbnail' => $defaultThumbnail,
			'typeNum' => (int) $this->settings['ajaxTypeNum']
		];
		$this->view->assignMultiple($assignedValues);

		return $this->htmlResponse();
	}



	/**
	 * Displays the selected content with ajax
	 *
	 */
	public function ajaxAction(): ResponseInterface
	{
		$post = $this->request->getParsedBody();

		if ( !empty($post['cookies'])) {
			$cookieExpire = $this->settings['cookieExpire'] ? (int)$this->settings['cookieExpire'] : 30;
			setcookie('t3scontentconsent_'.(int)$post['currentRecord'], 'allow', time() + (86400 * $cookieExpire), '/');
		}

		$conf ['tables'] = 'tt_content';
		$conf ['source'] = $post['contentByUid'];
		$conf ['dontCheckPid'] = 1;

		$cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);
		$data = $cObj->cObjGetSingle('RECORDS', $conf);

		return $this->responseFactory->createResponse()
			->withHeader('Content-Type', 'application/json')
			->withBody($this->streamFactory->createStream($data));
	}

}
