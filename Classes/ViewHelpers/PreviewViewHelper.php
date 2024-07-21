<?php

declare(strict_types=1);

namespace T3S\ContentConsent\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Information\Typo3Version;

/**
 * This file is part of the TYPO3 extension content_consent.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
class PreviewViewHelper extends AbstractViewHelper
{
	use CompileWithRenderStatic;

	/**
	 * @var bool
	 */
	protected $escapeOutput = false;

	public function initializeArguments()
	{
		parent::initializeArguments();
		$this->registerArgument('flexform', 'string', 'xml data', true);
	}

	public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext): string
	{
		$flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
		$flexFormDataArr = $flexFormService->convertFlexFormContentToArray($arguments['flexform']);
		$contentByUid = (int) $flexFormDataArr['settings']['consent']['contentByUid'];
		$contentConsent = BackendUtility::getRecord('tt_content', $contentByUid);
		$pageConsent = BackendUtility::getRecord('pages', $contentConsent['pid']);

		$typo3Version = GeneralUtility::makeInstance(Typo3Version::class);
		if ($typo3Version->getMajorVersion() < 13) {
			$editLink = '';
		} else {
			$request = $GLOBALS['TYPO3_REQUEST'];
			$uriParameters = [
				'edit' => [
					'tt_content' => [$contentByUid => 'edit'],
				],
				'returnUrl' => $request->getAttribute('normalizedParams')->getRequestUri(),
			];

			$backendUriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
			$editHref = $backendUriBuilder->buildUriFromRoute('record_edit', $uriParameters);
			$editLink = '<a href="'.$editHref.'"><span class="t3js-icon icon icon-size-small icon-state-default icon-actions-document-open" data-identifier="actions-document-open" aria-hidden="true"><span class="icon-markup"><svg class="icon-color"><use xlink:href="/_assets/1ee1d3e909b58d32e30dcea666dd3224/Icons/T3Icons/sprites/actions.svg#actions-document-edit"></use></svg></span></span>  Edit tt_content_'.$contentByUid.'</a>';
		}

		$preview = '<strong>Content Consent (loaded by Ajax):</strong></br></br>
		<table class="table table-striped">
			<tbody>
				<tr>
					<th scope="row" style="width:20%">Uid:</th>
					<td>'.$contentByUid.'</td>
				</tr>
				<tr>
					<th scope="row">CType:</th>
					<td>'.$contentConsent['CType'].'</td>
				</tr>
				<tr>
					<th scope="row">Header:</th>
					<td>'.$contentConsent['header'].'</td>
				</tr>
				<tr>
					<th scope="row">On page:</th>
					<td>'.$pageConsent['title'].' (uid: '.$contentConsent['pid'].')</td>
				</tr>
			</tbody>
		</table>
		<br/>'
		.$editLink;

		return $preview;
	}

}
