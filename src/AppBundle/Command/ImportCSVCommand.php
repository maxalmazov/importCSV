<?php
namespace AppBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportCSVCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('import:csv')
            ->setDescription('Import CSV file to database')
            ->setHelp('This command import CSV file to database')
            ->addArgument('filename', InputArgument::REQUIRED, 'The path to file.')
            ->addOption('test',null, InputOption::VALUE_NONE, 'Run test import without insert to database.')
        ;
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $io = new SymfonyStyle($input,$output);
        $io->note(array(
            'The file you upload must meet the following conditions:'
        ));
        $io->listing(array(
            'The first column must consist of the product code in the format is "P****", where * is digits.',
            'All items must be filled',
        ));
        $io->confirm('Do you want to continue?');

        if (!($input->getArgument('filename'))) {
            $question = new Question('<question>Choose the file (write path to file):</question> ', null);
            $filename  = $helper->ask($input, $output, $question);
            $input->setArgument('filename', $filename);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $now = new \DateTime();
        $output->writeln('<comment>Start: '.$now->format('d-m-Y G:i:s').'</comment>');

        $import = $this->getContainer()->get('import.csv');
        $import->import($input, $output);

        $now = new \DateTime();
        $output->writeln('<comment>End: '.$now->format('d-m-Y G:i:s').'</comment>');
    }
}