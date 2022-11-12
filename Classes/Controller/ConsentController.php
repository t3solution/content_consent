<?php
declare(strict_types=1);

namespace T3S\ContentConsent\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use Psr\Http\Message\ResponseInterface;
use T3SBS\T3sbootstrap\Domain\Repository\ConfigRepository;

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
		$contentConsent = FALSE;
		$currentRecord = $this->configurationManager->getContentObject()->data['uid'];
		$thumbnails = null;
		$t3sbSettings = [];

		if ( $this->settings['consent']['cookie'] && isset($_COOKIE['t3scontentconsent_'.$currentRecord]) && $_COOKIE['t3scontentconsent_'.$currentRecord] == 'allow' ) {
			$contentConsent = TRUE;
		} else {
			$fileRepository = GeneralUtility::makeInstance(FileRepository::class);
			$thumbnails = $fileRepository->findByRelation('tt_content', 'consentpreviewimage', $currentRecord);
			if ( ExtensionManagementUtility::isLoaded('t3sbootstrap') ) {
				$t3sbSettings = self::getT3sbSettings($fileRepository);
			}
		}

		$assignedValues = [
			'currentRecord' => $currentRecord,
			'contentConsent' => $contentConsent,
			'thumbnail' => empty($thumbnails[0]) ? FALSE : $thumbnails[0],
			't3sb' => $t3sbSettings
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



	/**
	 * Returns settings from EXT:t3sbootstrap if loaded
	 *
	 * @param FileRepository $fileRepository
	 *
	 * @return array
	 */
	private function getT3sbSettings($fileRepository): array
	{
		$contentByUid = (int) $this->settings['consent']['contentByUid'];
		$t3sbSettings = ['image'=>FALSE];
		$content = self::getData($contentByUid);

		if ($content['image_zoom'] && ( $content['assets'] || $content['image'])) {			
			$extconf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');
			$typoscript = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT,'t3sbootstrap');
			$lazyLoad = empty($extconf['lazyLoad']) ? FALSE : TRUE;
			$cdn = $typoscript['module.']['tx_t3sbootstrap.']['settings.']['cdn.']['enable'];
			$lightboxSelection = (int) GeneralUtility::makeInstance(ConfigRepository::class)->findAll()[0]->getLightboxSelection();
			if (empty($lightboxSelection)) {
				$lightboxSelection = 0;
			} else {
				if ($lightboxSelection == 1) {
					$lightboxVersion = $typoscript['module.']['tx_t3sbootstrap.']['settings.']['cdn.']['baguetteBox'];
				}
				if ($lightboxSelection == 2) {
					$lightboxVersion = $typoscript['module.']['tx_t3sbootstrap.']['settings.']['cdn.']['halkabox'];
				}
				if ($lightboxSelection == 3) {
					$lightboxVersion = $typoscript['module.']['tx_t3sbootstrap.']['settings.']['cdn.']['glightbox'];
				}
			}

			$t3sbSettings = [
				'image'=>TRUE,
				'lazyLoad'=>$lazyLoad,
				'cdn'=>$cdn,
				'lightboxSelection'=>$lightboxSelection,
				'lightboxVersion'=>$lightboxVersion
			];
		}

		return $t3sbSettings;
	}



	/**
	 * Returns data from tt_content
	 *
	 * @param int $contentByUid
	 *
	 * @return array
	 */
	public function getData($contentByUid): array
	{
		$queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
		$result = $queryBuilder
			->select('image_zoom', 'assets', 'image')
			->from('tt_content')
			->where(
				$queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($contentByUid, \PDO::PARAM_INT))
			)
			->executeQuery();
		
		return $result->fetchAssociative();
	}

}
