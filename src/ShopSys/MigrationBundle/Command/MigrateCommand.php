<?php

namespace ShopSys\MigrationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand extends ContainerAwareCommand {

	protected function configure() {
		$this
			->setName('shopsys:migrations:migrate')
			->setDescription('Execute all database migrations in one transaction.');
	}

	/**
	 * @param \Symfony\Component\Console\Input\InputInterface $input
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$em = $this->getContainer()->get('doctrine.orm.entity_manager');
		/* @var $em \Doctrine\ORM\EntityManager */

		$em->transactional(function () use ($output) {
			$this->executeDoctrineMigrateCommand($output);
		});
	}

	/**
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 */
	private function executeDoctrineMigrateCommand(OutputInterface $output) {
		$doctrineMigrateCommand = $this->getApplication()->find('doctrine:migrations:migrate');
		$arguments = [
			'command' => 'doctrine:migrations:migrate',
			'--allow-no-migration' => true,
		];
		$input = new ArrayInput($arguments);
		$input->setInteractive(false);

		$exitCode = $doctrineMigrateCommand->run($input, $output);

		if ($exitCode !== 0) {
			$message = 'Doctrine migration command did not exit properly (exit code is ' . $exitCode . ').';
			throw new \ShopSys\MigrationBundle\Command\Exception\MigrateCommandException($message);
		}
	}

}
