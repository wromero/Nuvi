<?php

namespace NS\ImportBundle\Command;

use Ddeboer\DataImport\Reader\ExcelReader;
use SplFileObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of CsvReaderCommand
 *
 * @author gnat
 */
class ExcelReaderCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('nsimport:readexcel')
            ->setDescription('Reads and analysis a CSV')
            ->addArgument('file', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filePath  = $input->getArgument('file');
        $file      = new SplFileObject($filePath);
        $csvReader = new ExcelReader($file);
        $csvReader->setHeaderRowNumber(0);

        $output->writeln("File Columns");
        foreach ($csvReader->getColumnHeaders() as $index => $column) {
            $output->writeln(sprintf("%d => '%s'", $index, $column));
        }

        $output->writeln(sprintf("%s has %d rows", $filePath, $csvReader->count()));
    }

}
