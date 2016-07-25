<?php
/**
 * Created by PhpStorm.
 * User: 67827
 * Date: 20.07.2016
 * Time: 13:45
 */

namespace nwlBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use nwlBundle\Entity\WhiteListRequest;
use nwlBundle\Entity\WhiteListTarget;
use Symfony\Component\HttpFoundation\Request;

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
        return $this->view($whiteListRequests);
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

        $user = $userService->getById($username);
        $whiteListTarget = $whiteListService->getOrCreateTargetByDomain($domain);

        //Check if request exists
        $whiteListRequest = $whiteListService->findWhiteListRequest($whiteListTarget, $user);
        if(null === $whiteListRequest){
            $whiteListRequest = new WhiteListRequest();
            $whiteListRequest->setUser($user);
            $whiteListRequest->setWhitelistTarget($whiteListTarget);
            $whiteListRequest->setReason($reason);
            $whiteListRequest->setCreated($created);

            $validator = $this->get('validator');
            $violationList = $validator->validate($whiteListRequest);
            if($violationList->count() == 0)
            {
                return $this->view($violationList,422);
            }
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
     *     {"name"="state", "dataType"="integer", "required"=true, "description"="state of the decision - allow or deny"},
     *     {"name"="admin", "dataType"="integer", "required"=true, "description"="username(id) of the admin"}}
     * )
     * @param Request $request
     * @param int $id
     */
    public function decideForWhiteListRequestAction(Request $request, $id)
    {

        $whiteListTarget = $this->getDoctrine()->getRepository('nwlBundle:WhiteListTarget')->find($id);
        $state = $request->request->get('state');
        $admin = $request->request->get('admin');

        $whiteListTarget->setState($state);
        $whiteListTarget->setDecidedBy($admin);
        $whiteListService = $this->get('nwl.whitelist');
        $whiteListService->decideWhiteListTargetState($whiteListTarget);

    }
}