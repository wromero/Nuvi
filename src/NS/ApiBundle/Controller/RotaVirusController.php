<?php

namespace NS\ApiBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as REST;

/**
 * Description of RotaVirusController
 *
 * @author gnat
 * @Route("/api/rota")
 */
class RotaVirusController extends CaseController
{
    /**
     * Get RotaVirus Case
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a case for a given id",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the case is not found"
     *   }
     * )
     *
     * @REST\View(templateVar="case",serializerGroups={"api"})
     * @REST\Get("/case/{objId}",name="nsApiRotaGetCase")
     *
     * @param string  $objId      the object id
     *
     * @return array
     *
     * @throws NotFoundHttpException when case not exist
    */
    public function getRotaCaseAction($objId)
    {
        return $this->getCase('rota',$objId);
    }

    /**
     * Retrieves an RotaVirus case lab by id. Most fields are returned, however some fields
     * if empty are excluded from the result set. For example the firstName and
     * lastName fields are only returned when there is data in them.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a case lab for a given id",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the case is not found"
     *   }
     * )
     *
     * @REST\View(templateVar="case",serializerGroups={"api"})
     * @REST\Get("/case/{objId}/lab",name="nsApiRotaGetLab")
     *
     * @param string  $objId      the object id
     *
     * @return array
     *
     * @throws NotFoundHttpException when case not exist
     * @throws NonExistentCase when case doees not exist
    */
    public function getRotaCaseLabAction($objId)
    {
        return $this->getCase('rota',$objId);
    }

    /**
     * Patch RotaVirus Case
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Patch RotaVirus case",
     *   input = "rotavirus",
     *   statusCodes = {
     *         202 = "Returned when successful",
     *         406 = "Returned when there are validation issues with the case",
     *          }
     * )
     *
     * @REST\Patch("/case/{objId}",name="nsApiRotaPatchCase")
     * @REST\View()
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $objId
     *
     */
    public function patchRotaCaseAction(Request $request, $objId)
    {
        $route = $this->generateUrl('nsApiRotaGetCase', array('id' => $objId));

        return $this->updateCase($request, $objId, 'PATCH', 'rotavirus', 'NSSentinelBundle:RotaVirus', $route);
    }

    /**
     * Patch RotaVirus Lab Data,
     *
     * @ApiDoc(
     *  resource = true,
     *  description = "Updates a RotaVirus Lab data",
     *  input = "rotavirus_lab",
     *  statusCodes={
     *         202 = "Returned when successful",
     *         406 = "Returned when there is an issue with the form data"
     *         }
     * )
     * @REST\Patch("/case/{objId}/lab",name="nsApiRotaPatchLab")
     * @REST\View()
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $objId
     */
    public function patchRotaLabAction(Request $request, $objId)
    {
        $route = $this->generateUrl('nsApiRotaGetLab', array('id' => $objId));

        return $this->updateLab($request, $objId, 'PATCH', 'rotavirus_lab', 'NSSentinelBundle:RotaVirus', $route);
    }

    /**
     * Patch RotaVirus Outcome Data
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Patch RotaVirus Outcome data",
     *   input = "rotavirus_outcome",
     *   statusCodes = {
     *         202 = "Returned when successful",
     *         406 = "Returned when there are validation issues with the case",
     *          }
     * )
     *
     * @REST\Patch("/case/{objId}/outcome",name="nsApiRotaPatchOutcome")
     * @REST\View()
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $objId
     *
     */
    public function patchRotaOutcomeAction(Request $request, $objId)
    {
        $route = $this->generateUrl('nsApiRotaGetCase', array('id' => $objId));

        return $this->updateCase($request, $objId, 'PATCH', 'rotavirus_outcome', 'NSSentinelBundle:RotaVirus', $route);
    }

    /**
     * Put RotaVirus Case
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Put RotaVirus case",
     *   input = "rotavirus",
     *   statusCodes = {
     *         202 = "Returned when successful",
     *         406 = "Returned when there are validation issues with the case",
     *          }
     * )
     *
     * @REST\Put("/case/{objId}",name="nsApiRotaPutCase")
     * @REST\View()
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $objId
     *
     */
    public function putRotaCaseAction(Request $request, $objId)
    {
        $route = $this->generateUrl('nsApiRotaGetCase', array('id' => $objId));

        return $this->updateCase($request, $objId, 'PUT', 'rotavirus', 'NSSentinelBundle:RotaVirus', $route);
    }

    /**
     * Put RotaVirus Lab Data,
     *
     * @ApiDoc(
     *  resource = true,
     *  description = "Updates a RotaVirus Lab data",
     *  input = "rotavirus_lab",
     *  statusCodes={
     *         202 = "Returned when successful",
     *         406 = "Returned when there is an issue with the form data"
     *         }
     * )
     *
     * @REST\Put("/case/{objId}/lab",name="nsApiRotaPutLab")
     * @REST\View()
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $objId
     */
    public function putRotaLabAction(Request $request, $objId)
    {
        $route = $this->generateUrl('nsApiRotaGetLab', array('id' => $objId));

        return $this->updateLab($request, $objId, 'PUT', 'rotavirus_lab', 'NSSentinelBundle:RotaVirus', $route);
    }

    /**
     * Put RotaVirus Outcome Data
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Put RotaVirus Outcome data",
     *   input = "rotavirus_outcome",
     *   statusCodes = {
     *         202 = "Returned when successful",
     *         406 = "Returned when there are validation issues with the case",
     *          }
     * )
     *
     * @REST\Put("/case/{objId}/outcome",name="nsApiRotaPutOutcome")
     * @REST\View()
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $objId
     *
     */
    public function putRotaOutcomeAction(Request $request, $objId)
    {
        $route = $this->generateUrl('nsApiRotaGetCase', array('id' => $objId));

        return $this->updateCase($request, $objId, 'PUT', 'rotavirus_outcome', 'NSSentinelBundle:RotaVirus', $route);
    }

    /**
     * Create a RotaVirus Case,
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new Rotavirus case",
     *   input = "create_rotavirus"
     * )
     *
     * @REST\Post("/case",name="nsApiRotaPostCase")
     * @REST\View()
     *
     * @param Request $request the request object
    */
    public function postRotaCaseAction(Request $request)
    {
        return $this->postCase($request,'nsApiRotaGetCase','create_rotavirus','NSSentinelBundle:RotaVirus');
    }
}
