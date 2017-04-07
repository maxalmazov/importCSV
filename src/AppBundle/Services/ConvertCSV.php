<?php

namespace AppBundle\Services;

use AppBundle\Error\ErrorImport;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class ConvertCSV
{
    public function convert($filename, OutputInterface $output)
    {
        if (file_exists($filename) && is_readable($filename) && $this->isCSVFile($filename)) {
            $handle = fopen($filename, 'r');
            $header = null;
            $i = 1;
            $invalidItems = [];

            while (($row = fgetcsv($handle, 0)) !== FALSE) {
                if (!$header) {
                    $header=$row;
                } else {
                    if (count($header) === count($row)) {
                        $arr[] = array_combine($header, $row);
                    } else {
                        $error = new ErrorImport();
                        $error->setProductCode($row[0]);
                        $error->setMessage('Item do not completely filled');
                        $invalidItems[] = $error;
                    }
                }
                $i++;
            }

            fclose($handle);

        } else {
            throw new FileNotFoundException($filename);
        }

        if (count($arr) === 0) {
            throw new \Exception('File '.$filename.' is empty');
        }
        return [$arr, $invalidItems];
    }

    private function isCSVFile($filename)
    {
        $file = new \SplFileInfo($filename);

        if ($file->getExtension() === 'csv') {
            return true;
        }
        return false;
    }
}