<?php
/**
 * Created by PhpStorm.
 * User: 67827
 * Date: 20.07.2016
 * Time: 13:45
 */

namespace nwlBundle\Controller;

use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use nwlBundle\Entity\WhiteListRequest;
use nwlBundle\Entity\WhiteListTarget;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\ConstraintViolationList;

class WhiteListController extends FOSRestController
{
    /**
     * @Get(path="/whitelist-request")
     * @ApiDoc(
     *     section="WhiteListRequest",
     *     description="list of all WhiteListRequests"
     * )
     */
    public function listWhiteListRequestsAction()
    {
        $whiteListService = $this->get('nwl.whitelist');
        $whiteListRequests = $whiteListService->findAllWhiteListRequests();
        $view = $this->view($whiteListRequests);
        //$context = SerializationContext::create()->setGroups(array('Default','user'));
        $view->getContext()->setGroups(array('Default','user'));
        return $view;
    }


    /**
     * @Get(path="/whitelist-target", name="whitelistTarget.all")
     * @ApiDoc(
     *     section="WhiteListTarget",
     *     description="list of all WhiteListTargets"
     * )
     */
    public function listWhiteListTargetsAction(Request $request)
    {
        $userService = $this->get('nwl.user');
        $apikey = $request->headers->get('apikey');
        if(null == $apikey)
        {
            return new Response('API-Key not found, please authorize', 401);
        }
        if(!$userService->isApiKeyForAdmin($apikey)){
            return new Response('Invalid API-Key for Interaction', 403);
        }
        $whiteListService = $this->get('nwl.whitelist');
        $whiteListTargetDTOs = $whiteListService->findAllWhiteListTargets();
        $view = $this->view($whiteListTargetDTOs);
        //$context = SerializationContext::create()->setGroups(array('Default','target'));
        $view->getContext()->setGroups(array('Default','target'));
        return $view;
    }

    /**
     * @Get(path="/user/{username}/whitelist-request")
     * @ApiDoc(
     *     section="WhiteListRequest",
     *     description="list all WhiteList-Request from the user with {username}",
     *     requirements={
     *          {"name"="username", "dataType"="string", "description"="specified user"}
     *     }
     * )
     *
     * @param string $username
     * @return \FOS\RestBundle\View\View
     */
    public function listWhiteListRequestAction($username)
    {
        //All Services
        $userService = $this->get('nwl.user');
        $whiteListService = $this->get('nwl.whitelist');

        $user = $userService->getById($username);

        $whiteListRequests = $whiteListService->findWhiteListRequestsByUsername($user);
        return $this->view($whiteListRequests);
    }

    /**
     * @Post(path="/user/{username}/whitelist-request")
     * @ApiDoc(
     *     section="WhiteListRequest",
     *     description="creates a new WhiteListRequest",
     *     parameters={
     *          {"name"="domain", "dataType"="string", "required"=true, "description"="Domain to unlock"},
     *          {"name"="reason", "dataType"="string", "required"=true, "description"="Justification for unlocking the Domain"}
     *     },
     *     requirements={
     *          {"name"="username", "dataType"="string", "description"="user who creates the request"}
     *     }
     * )
     * @param Request $request
     * @param string $username
     * @return \FOS\RestBundle\View\View
     */
    public function createWhiteListRequestAction(Request $request, $username)
    {
        //All Parameters
        $domain = $request->request->get('domain');
        $reason = $request->request->get('reason');
        $created = new \DateTime();

        //All Services
        $userService = $this->get('nwl.user');
        $whiteListService = $this->get('nwl.whitelist');
        $validator = $this->get("validator");

        $constraintViolations = new ConstraintViolationList();

        //TODO Verification - NULL
        $user = $userService->getById($username);

        //Create Datamodel Dummies
        $whiteListTarget = new WhiteListTarget();
        $whiteListTarget->setDomain($domain);

        $whiteListRequest = new WhiteListRequest();
        $whiteListRequest->setUser($user);
        $whiteListRequest->setWhitelistTarget($whiteListTarget);
        $whiteListRequest->setReason($reason);
        $whiteListRequest->setCreated($created);

        //Validate Datamodel
        foreach ($validator->validate($whiteListTarget) as $error) {
            $constraintViolations->add($error);
        }
        foreach ($validator->validate($whiteListRequest) as $error) {
            $constraintViolations->add($error);
        }

        if($constraintViolations->count() != 0) {
            return $this->view($constraintViolations, 422);
        }

        //Insert Data into Database
        $whiteListTarget = $whiteListService->getOrCreateTargetByDomain($domain);

        //Check if request exists
        $tmpWhiteListRequest = $whiteListService->findWhiteListRequest($whiteListTarget, $user);
        if(null === $tmpWhiteListRequest){
            $whiteListRequest = $whiteListService->createWhiteListRequest($whiteListRequest);
        }
        return $this->view($whiteListRequest);
    }


    /**
     * @Post(path="/whitelist-target/{id}")
     * @ApiDoc(
     *     section="WhiteListTarget",
     *     description="allows or denies permission for the Domain of the Whitelist-Target with {id}",
     *     parameters={
     *     {"name"="state", "dataType"="integer", "required"=true, "description"="state of the decision - allow or deny"}
     *     }
     * )
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function decideForWhiteListRequestAction(Request $request, $id)
    {
        $userService = $this->get('nwl.user');
        $whiteListTarget = $this->getDoctrine()->getRepository('nwlBundle:WhiteListTarget')->find($id);
        $state = $request->request->get('state');
        $apikey = $request->headers->get('apikey');
        $admin = $userService->getUserByApiKey($apikey);

        if(null === $whiteListTarget || null === $admin || null === $state) {
            return $this->view("The given paramters for deciding the state are not valid.", 422);
        }
        $whiteListTarget->setDecisionDate( new \DateTime());
        $whiteListTarget->setState($state);
        $whiteListTarget->setDecidedBy($admin);

        $this->get('doctrine.orm.default_entity_manager')->flush();
        return new Response('OK');
    }
}