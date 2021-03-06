<?php
/**
 * Created by PhpStorm.
 * User: gnat
 * Date: 19/05/16
 * Time: 4:03 PM
 */

namespace NS\SentinelBundle\Controller;

use Doctrine\DBAL\DBALException;
use NS\SentinelBundle\Entity\ZeroReport;
use NS\SentinelBundle\Filter\Type\ZeroReportFilterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ZeroReportingController
 * @package NS\SentinelBundle\Controller
 *
 * @Route("/{_locale}")
 */
class ZeroReportingController extends Controller
{
    const SESSION_KEY = 'zero.reporting';

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/zero-report",name="zeroReportIndex")
     */
    public function indexAction(Request $request)
    {
        $filterForm = $this->createForm(ZeroReportFilterType::class);
        $filterForm->handleRequest($request);
        $request->getSession()->remove('zero.reporting');
        if ($filterForm->isValid()) {
            $request->getSession()->set('zero.reporting', $filterForm->getData());

            return $this->redirect($this->generateUrl('zeroReportUpdate'));
        }

        return $this->render('NSSentinelBundle:ZeroReport:index.html.twig', array('form' => $filterForm->createView()));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/zero-report/update",name="zeroReportUpdate")
     */
    public function updateAction(Request $request)
    {
        $data = $request->getSession()->get('zero.reporting', false);
        if ($data === false) {
            $this->get('ns_flash')->addError('Error', 'Unable to find zero reporting criteria');
            return $this->redirect($this->generateUrl('zeroReportIndex'));
        }

        $reporter = $this->get('ns_sentinel.zero_reporter');

        if ($request->getMethod() === 'POST') {
            $submittedData = $request->request->get('zeroReport');
            if (!empty($submittedData)) {
                $reporter->updateZeroReports($submittedData,$data);

                $this->get('ns_flash')->addSuccess('Success', 'Updated Zero Reports');
            } else {
                $this->get('ns_flash')->addWarning('Note', 'No changes were made');
            }

            return $this->redirect($this->generateUrl('zeroReportUpdate'));
        }

        $results = $reporter->getZeroReports($data['type'], $data['from'], $data['to']);
        $filterForm = $this->createForm(ZeroReportFilterType::class);

        return $this->render('NSSentinelBundle:ZeroReport:index.html.twig', array('form' => $filterForm->createView(), 'results' => $results));
    }
}
