<?php

namespace AppBundle\Services;

use AppBundle\Entity\Product;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use \Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class ImportCSV
{

    private $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    public function import(InputInterface $input, OutputInterface $output)
    {
        try {
            $arrayFromCsv = $this->getData($input, $output);
            $data = $arrayFromCsv[0];
            $errorConvert = $arrayFromCsv[1];

            $em = $this->container->get('doctrine')->getManager();
            $em->getConnection()->getConfiguration()->setSQLLogger(null);

            $size = count($data)+count($errorConvert);
            $batchSize = 50;
            $i = 1;

            $progress = new ProgressBar($output, $size);
            $progress->start();

            $invalidItems = array();
            foreach ($data as $row) {
                if ($this->validateCSV($row) instanceof Product) {
                    $product = $this->validateCSV($row);

                    $em->persist($product);

                    if (($i % $batchSize) === 0 && !($input->getOption('test'))) {
                        $em->flush();
                        $em->clear();

                        $progress->advance($batchSize);

                        $now = new \DateTime();
                        $output->writeln(' of products processed ... | ' . $now->format('G:i:s'));
                    }
                    $i++;
                } else {
                    $invalidItems[] = $this->validateCSV($row);
                    if (($i % $batchSize) === 0) {
                        $progress->advance($batchSize);
                    }
                    $i++;
                }
            }
            if (!($input->getOption('test'))) {
                $em->flush();
            }

            $em->clear();
            $progress->finish();
            $errors = array_merge($invalidItems, $errorConvert);

            $this->showReportImport($output, $data, $errors);

        } catch (FileNotFoundException $exception) {
            $output->writeln('<error>'.$exception->getMessage().'</error>');
        } catch (\Exception $exception) {
            $output->writeln('<error>'.$exception->getMessage().'</error>');
        }
    }

    private function getData(InputInterface $input, OutputInterface $output)
    {
        $filename = $input->getArgument('filename');

        $converter = $this->container->get('convert.csv');
        $data = $converter->convert($filename, $output);

        return $data;
    }

    private function validateCSV($row)
    {
        $validator = $this->container->get('validator.csv');
        $product = $validator->validate($row);

        return $product;
    }

    private function showReportImport(OutputInterface $output, $data, $errors)
    {
        $import = count($data)-count($errors);
        $notImport = count($errors);
        $output->writeln("\n".'<info>"'.$import.'"</info> items was successfully imported.');
        $output->writeln('Next items (<info>'.$notImport.'</info>) was not imported:');

        foreach ($errors as $item) {
            $output->writeln($item);
        }
    }
}