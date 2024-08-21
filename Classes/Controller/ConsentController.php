<?php

namespace T3S\ContentConsent\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Core\Cache\CacheManager;

/*
 * This file is part of the TYPO3 extension content_consent.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class ConsentController extends ActionController
{

	public function __construct(
		private readonly FileRepository $fileRepository,
		private readonly ContentObjectRenderer $contentObjectRenderer,
		private readonly Typo3Version $typo3Version,
		private readonly CacheManager $cacheManager
	) {}


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

			// Custom thumbnail
			if ( !empty($this->settings['consent']['thumbnail']) ) {

				$relatedCustomThumbnails = $this->fileRepository->findByRelation('tt_content', 'settings.consent.thumbnail', $currentRecord);
				$customThumbnail = !empty($relatedCustomThumbnails[0]) ? $relatedCustomThumbnails[0] : [];
				$defaultThumbnail = [];
				if ( empty($customThumbnail) ) {
					// if media is hidden -> use default
					$defaultThumbnail = $this->fileRepository->findByRelation('tt_content', 'assets', (int) $this->settings['consent']['contentByUid'])[0];
				}
			}

			// Default thumbnail
			if ( !empty($this->settings['consent']['defaultThumbnail']) ) {

				$relatedDefaultThumbnails = $this->fileRepository->findByRelation('tt_content', 'assets', (int) $this->settings['consent']['contentByUid']);
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

		if ($this->typo3Version->getMajorVersion() < 13) {
			$hash = GeneralUtility::hmac($this->settings['consent']['contentByUid'], self::class);
		} else {
			$hash = $this->hashService->hmac($this->settings['consent']['contentByUid'], self::class);
		}

		$assignedValues = [
			'currentRecord' => $currentRecord,
			'contentConsent' => $contentConsent,
			'extension' => $extension,
			'type' => $type,
			'customThumbnail' => $customThumbnail,
			'defaultThumbnail' => $defaultThumbnail,
			'hash' => $hash,
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
		$success = FALSE;
		$post = $this->request->getParsedBody();

		if (!empty($post)) {
			if ($this->typo3Version->getMajorVersion() < 13) {
				$expected = GeneralUtility::hmac($post['contentByUid'], self::class);
				$success = hash_equals($expected, $post['hash']);
			} else {
				$expected = $this->hashService->hmac($post['contentByUid'], self::class);
				$isValidHash = $this->hashService->validateHmac($post['contentByUid'], self::class, $expected);
				if ($isValidHash) {
					$success = hash_equals($expected, $post['hash']);
				}
			}
		}

		if (!empty($success)) {
			if ( !empty($post['cookies'])) {
				$cookieExpire = $this->settings['cookieExpire'] ? (int)$this->settings['cookieExpire'] : 30;
				setcookie('t3scontentconsent_'.(int)$post['currentRecord'], 'allow', time() + (86400 * $cookieExpire), '/');
			}
			$conf['tables'] = 'tt_content';
			$conf['source'] = (int)$post['contentByUid'];
			$conf['dontCheckPid'] = 1;
			$data = $this->contentObjectRenderer->cObjGetSingle('RECORDS', $conf);

			$this->cacheManager->flushCachesInGroup('pages');

			return $this->responseFactory->createResponse()
				->withHeader('Content-Type', 'application/text')
				->withBody($this->streamFactory->createStream($data));

		} else {

			$data = '<div class="alert alert-danger" role="alert">No Success!</div>';
			return $this->responseFactory->createResponse()
				->withHeader('Content-Type', 'application/text')
				->withBody($this->streamFactory->createStream($data));
		}
	}

}
