<?php

namespace NS\ImportBundle\Importer;

use \Doctrine\Common\Persistence\ObjectManager;
use \NS\ImportBundle\Entity\Import;
use \NS\ImportBundle\Filter\Duplicate;
use \NS\ImportBundle\Filter\NotBlank;

/**
 * Class ImportBatchWorker
 * @package NS\ImportBundle\Importer
 */
class ImportBatchWorker
{
    /**
     * @var ObjectManager
     */
    private $entityMgr;

    /**
     * @var ImportProcessor
     */
    private $processor;

    /**
     * ImportBatchWorker constructor.
     * @param ObjectManager $entityMgr
     * @param ImportProcessor $processor
     */
    public function __construct(ObjectManager $entityMgr, ImportProcessor $processor)
    {
        $this->entityMgr = $entityMgr;
        $this->processor = $processor;
    }

    /**
     * @param int           $id             Import id
     * @param int|Number    $batchSize      Number of rows to process at a time
     *
     * @return bool Returns true when the import has been completely processed
     */
    public function consume($id, $batchSize = 500)
    {
        $import = $this->entityMgr->getRepository('NSImportBundle:Import')->find($id);

        if (!$import) {
            throw new \InvalidArgumentException(sprintf('Unable to find import %d',$id));
        }

        $this->processor->setDuplicate(new Duplicate(array('getcode' => 'site', 1 => 'caseId'), $import->getDuplicateFile()));
        $this->processor->setNotBlank(new NotBlank(array('caseId', 'site')));
        $this->processor->setLimit($batchSize);

        $result = $this->processor->process($import);
        $this->entityMgr->flush();

        $updater = new ImportResultUpdater();
        $updater->update($import, $result, $this->processor->getWriter($import->getClass())->getResults());

        if ($import->isComplete()) {
            $import->setPheanstalkStatus(Import::STATUS_COMPLETE);
        } else {
            $import->setPheanstalkStatus(Import::STATUS_RUNNING);
        }

        $this->entityMgr->persist($import);
        $this->entityMgr->flush();

        return $import->isComplete();
    }
}
