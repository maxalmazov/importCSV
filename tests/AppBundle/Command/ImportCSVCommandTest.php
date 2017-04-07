<?php

namespace Tests\AppBundle\Command;

use AppBundle\Command\ImportCSVCommand;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ImportCSVCommandTest extends KernelTestCase
{

    public function testExecute()
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $application->add(new ImportCSVCommand());

        $command = $application->find('import:csv');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),
            'filename' => 'web/stock.csv',
        ));

        $output = $commandTester->getDisplay();
        $this->assertContains('Exception: File not found', $output);
    }
}