<?php

namespace NS\SentinelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{

    /**
     * @Route("/login", name="admin_login")
     * @Template()
     */
    public function loginAction(Request $request)
    {
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR))
        {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        }
        else
        {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return array(
            // last username entered by the user
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        );
    }

    /**
     * @Route("/switchLanguage", name="switchLangugae")
     * @Template()
     */
    public function switchLanguageAction(Request $request)
    {
        $session       = $request->getSession();
        $currentLocale = $session->get('_locale');
        $locale        = ($currentLocale == 'en') ? 'fr' : 'en';

        $session->set('_locale', $locale);
        return $this->redirect($this->generateUrl('user_dashboard', array('_locale' => $locale)));
    }

    /**
     * @Route("/{_locale}",name="homepage")
     * @Template()
     */
    public function homepageAction(Request $request)
    {
        $repo        = $this->get('ns.model_manager')->getRepository("NSSentinelBundle:IBD");
        $byCountry   = $repo->getByCountry();
        $bySite      = $repo->getBySite();
        $byDiagnosis = $repo->getByDiagnosis();
        $form        = $this->createForm('IBDFieldPopulationFilterType', null, array(
            'site_type' => 'advanced'));
        $s           = $this->get('ns.sentinel.services.report');
        $cResult     = $s->getCulturePositive($request, $form, 'homepage');

        return array('byCountry'   => $byCountry, 'bySite'      => $bySite, 'byDiagnosis' => $byDiagnosis,
            'cResult'     => $cResult['results']);
    }

    /**
     * @Route("/",name="homepage_redirect")
     */
    public function homepageRedirectAction(Request $request)
    {
        return $this->get('ns.sentinel.services.homepage')->getHomepageResponse($request);
    }

}
