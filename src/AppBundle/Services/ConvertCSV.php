<?php

namespace AppBundle\Services;

class ConvertCSV
{
    public function convert($filename, $output)
    {
        if (file_exists($filename) && is_readable($filename)) {
            $handle = fopen($filename, 'r');
            $header = null;
            $i = 1;
            $invalidStr = '';

            while (($row = fgetcsv($handle, 0)) !== FALSE) {
                if (!$header) {
                    $header=$row;
                } else {
                    if (count($header) == count($row)) {
                        $arr[] = array_combine($header, $row);
                    } else {
                        $invalidStr.= $i.' ';
                    }
                }
                $i++;
            }
            fclose($handle);
            if (!($invalidStr === '')) {
                $output->writeln(
                    'These lines should have '.count($header)
                    .' columns (it is quantity of headers): "<info>'.$invalidStr.
                    '</info>" These lines will not imported'
                );
            }
        } else {
            throw new \Exception('File doesn\'t exist');
        }
        return $arr;
    }
}