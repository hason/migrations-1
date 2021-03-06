<?php

namespace ShopSys\MigrationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckDatabaseSchemaCommand extends ContainerAwareCommand {

	const RETURN_CODE_OK = 0;
	const RETURN_CODE_ERROR = 1;

	protected function configure() {
		$this
			->setName('shopsys:migrations:check-schema')
			->setDescription('Check if database schema is satisfying ORM');
	}

	/**
	 * @param \Symfony\Component\Console\Input\InputInterface $input
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$databaseSchemaFacade = $this->getContainer()->get('shopsys.migration.component.doctrine.database_schema_facade');
		/* @var $databaseSchemaFacade \ShopSys\MigrationBundle\Component\Doctrine\DatabaseSchemaFacade */

		$output->writeln('Checking database schema...');

		$filteredSchemaDiffSqlCommands = $databaseSchemaFacade->getFilteredSchemaDiffSqlCommands();
		if (count($filteredSchemaDiffSqlCommands) === 0) {
			$output->writeln('<info>Database schema is satisfying ORM.</info>');
		} else {
			$output->writeln('<error>Database schema is not satisfying ORM!</error>');
			$output->writeln('<error>Following SQL commands should fix the problem (revise them before!):</error>');
			$output->writeln('');
			foreach ($filteredSchemaDiffSqlCommands as $sqlCommand) {
				$output->writeln('<fg=red>' . $sqlCommand . ';</fg=red>');
			}
			$output->writeln('');

			return self::RETURN_CODE_ERROR;
		}

		return self::RETURN_CODE_OK;
	}

}
