<?php

declare(strict_types=1);

namespace T3S\ContentConsent\Upgrades;

use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Utility\GeneralUtility;

#[UpgradeWizard('contentConsent_pluginListTypeToCTypeUpdate')]
final class PluginListTypeToCTypeUpdate implements UpgradeWizardInterface
{
 
	public function getTitle(): string
	{
		return 'EXT:content_consent: Migrate list_type plugin to CType';
	}

	public function getDescription(): string
	{
		return 'The plugin content element (list) and the plugin sub types field (list_type) have been marked as deprecated in TYPO3 v13.4 and will be removed in TYPO3 v14.0.';
	}

	public function executeUpdate(): bool
	{

		$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
		$queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');

		$statements = $queryBuilder
			->select('uid', 'CType', 'list_type')
			->from('tt_content')
			->where(
				$queryBuilder->expr()->eq('CType', $queryBuilder->createNamedParameter('list', Connection::PARAM_STR)),
				$queryBuilder->expr()->eq('list_type', $queryBuilder->createNamedParameter('contentconsent_all', Connection::PARAM_STR))
			)
			->executeQuery()
			->fetchAllAssociative();
	
		foreach($statements as $key=>$statement) {		
			$queryBuilder
			->update('tt_content')
			->where($queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($statement['uid'], Connection::PARAM_INT)))
			->set('CType', 'contentconsent_all')
			->set('list_type', '')
			->executeStatement();
		}

		return TRUE;
	}


	public function updateNecessary(): bool
	{
		$updateNeeded = FALSE;
		// Check if the database table even exists
		if ($this->checkIfWizardIsRequired()) {
			$updateNeeded = TRUE;
		}

		return $updateNeeded;
	}


	public function getPrerequisites(): array
	{
		return [];
	}


	protected function checkIfWizardIsRequired(): bool
	{
		$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
		$queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');

		$numberOfCEs = $queryBuilder
			->count('uid')
			->from('tt_content')
			->where(
				$queryBuilder->expr()->eq('CType', $queryBuilder->createNamedParameter('list', Connection::PARAM_STR)),
				$queryBuilder->expr()->eq('list_type', $queryBuilder->createNamedParameter('contentconsent_all', Connection::PARAM_STR))
			)
			->executeQuery()
			->fetchOne();

		if ( $numberOfCEs > 0 ) {
			
			return TRUE;
		}

		return FALSE;
	}	

}
