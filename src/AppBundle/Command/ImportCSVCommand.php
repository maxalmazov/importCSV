<?php
namespace AppBundle\Command;

use AppBundle\Entity\Product;
use Symfony\Component\Console\Command\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Helper\ProgressBar;
use AppBundle\Services;

class ImportCSVCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('import:csv')
            ->setDescription('Import CSV file to database')
            ->setHelp('This command import CSV file, parsing it and insert to database')
            ->addArgument('filename', InputArgument::REQUIRED, 'The path to file.')
            ->addOption('option',null, InputOption::VALUE_REQUIRED, 'Run test import without insert to database.', null)
        ;
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        if (!($input->getArgument('filename'))) {
            $question = new Question('<question>Choose the file (write path to file):</question> ', null);
            $filename  = $helper->ask($input,$output,$question);
            $input->setArgument('filename', $filename);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $now = new \DateTime();
        $output->writeln('<comment>Start : ' . $now->format('d-m-Y G:i:s') . '</comment>');

        $this->import($input, $output);

        $now = new \DateTime();
        $output->writeln('<comment>End : ' . $now->format('d-m-Y G:i:s') . '</comment>');
    }

    private function import(InputInterface $input, OutputInterface $output)
    {
        $data = $this->getDate($input, $output);

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        $size = count($data);
        $batchSize = 50;
        $i = 1;

        $progress = new ProgressBar($output, $size);
        $progress->start();

        $invalidLine = array();
        foreach ($data as $row) {
            if ($this->validateCSV($row) instanceof Product) {
                $product = $this->validateCSV($row);

                $em->persist($product);

                if (($i % $batchSize) === 0 && !($input->getOption('option') === 'test')) {
                    $em->flush();
                    $em->clear();

                    $progress->advance($batchSize);

                    $now = new \DateTime();
                    $output->writeln(' of products processed ... | ' . $now->format('G:i:s'));
                }
                $i++;

            } else {
                $invalidLine[] = $this->validateCSV($row);
                if (($i % $batchSize) === 0) {
                    $progress->advance($batchSize);
                }
                $i++;
            }
        }
        if (!($input->getOption('option') === 'test')) {
            $em->flush();
        }

        $em->clear();

        $progress->finish();

        $import = count($data)-count($invalidLine);
        $notImport = count($invalidLine);
        $output->writeln("\n".'<info>"'.$import.'"</info> was successfully imported.'."\n".'<info>"'.$notImport.'"</info> was not imported.');
    }

    private function getDate(InputInterface $input, OutputInterface $output)
    {
        $filename = $input->getArgument('filename');

        $converter = $this->getContainer()->get('import.csv');
        $data = $converter->convert($filename, $output);

        return $data;
    }

    private function validateCSV($row)
    {
        $validator = $this->getContainer()->get('validator.csv');
        $product = $validator->validate($row);

        return $product;
    }
}