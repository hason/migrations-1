<?php

namespace ShopSys\MigrationBundle\Command;

use Doctrine\Bundle\MigrationsBundle\Command\DoctrineCommand;
use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class GetCountOfMigrationsToExecuteCommand extends ContainerAwareCommand {

	protected function configure() {
		$this
			->setName('shopsys:migrations:count')
			->setDescription('Get count of migrations to execute.');
	}

	/**
	 * @param \Symfony\Component\Console\Input\InputInterface $input
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
		/* @var $em \Doctrine\ORM\EntityManager */
		$migrationsConfiguration = new Configuration($em->getConnection());

		DoctrineCommand::configureMigrations($this->getContainer(), $migrationsConfiguration);

		$migratedVersions = $migrationsConfiguration->getMigratedVersions();
		$availableVersions = $migrationsConfiguration->getAvailableVersions();

		$newMigrationsCount = count(array_diff($availableVersions, $migratedVersions));

		$output->writeln('Count of migrations to execute: ' . $newMigrationsCount);
	}

}
