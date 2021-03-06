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
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use nwlBundle\Entity\WhiteListRequest;
use nwlBundle\Entity\WhiteListTarget;
use nwlBundle\Service\WhiteListService;
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
     * @Get(path="/{age}/whitelist-target", name="whitelistTarget.all")
     * @ApiDoc(
     *     section="WhiteListTarget",
     *     description="list of all WhiteListTargets"
     * )
     * @param Request $request
     * @param $age
     * @return \FOS\RestBundle\View\View|Response
     */
    public function listWhiteListTargetsAction(Request $request, $age = WhiteListService::AGE_NEW)
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
        $whiteListTargetDTOs = $whiteListService->findAllWhiteListTargets($age);
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
     * @Route("/user/{username}/whitelist-request", options={"expose"=true}, name="_route")
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

        $resultFromDomainValidation = $this->validateDomain($domain);
        $domain = $resultFromDomainValidation;

        $whiteListTarget->setDomain($domain);
        //Insert Data into Database
        $whiteListTarget = $whiteListService->getOrCreateTargetByDomain($domain);

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

        //Check if request exists
        $tmpWhiteListRequest = $whiteListService->findWhiteListRequest($whiteListTarget, $user);
        if(null === $tmpWhiteListRequest){
            $whiteListRequest = $whiteListService->createWhiteListRequest($whiteListRequest);
        }
        return $this->view($whiteListRequest);
    }

    function validateDomain($domain){

        if(!strpos($domain,'://')){
            $domain = "http://".$domain;
        }

        $domain .= '/';
        $protocol_i = null;
        $remaining_url = null;
        $domain_i = null;
        $domain_parts = null;

        $parsed_url = array(
            'protocol'=>'',
            'domain'=>'',
            'path'=>'',
            'tld'=>'',
            'parent_domain'=>'',
            'subdomain'=>'',
            'host'=>''
        );
        $protocol_i = strrpos($domain,'://');
        $parsed_url['protocol'] = $protocol_i;
        $remaining_url = substr($domain,$protocol_i+3,strlen($domain));
        $domain_i = strpos($remaining_url,'/');
        $domain_i = $domain_i == -1 ? strlen($remaining_url) -1 : $domain_i;
        $parsed_url['domain'] = substr($remaining_url,0,$domain_i);
        $parsed_url['path'] = $domain_i == -1 || $domain_i + 1 == strlen($remaining_url) ? null : substr($remaining_url,$domain_i+1,strlen($remaining_url));
        $domain_parts = explode(".",$parsed_url['domain']);
        switch (sizeof($domain_parts)){
            case 2:
                $parsed_url['subdomain'] = null;
                $parsed_url['host'] = $domain_parts[0];
                $parsed_url['tld'] = $domain_parts[1];
                break;
            case 3:
                $parsed_url['subdomain'] = $domain_parts[0];
                $parsed_url['host'] = $domain_parts[1];
                $parsed_url['tld'] = $domain_parts[2];
                break;
            case 4:
                $parsed_url['subdomain'] = $domain_parts[0];
                $parsed_url['host'] = $domain_parts[1];
                $parsed_url['tld'] = $domain_parts[2].".".$domain_parts[3];
                break;
        }
        $parsed_url['parent_domain'] = $parsed_url['host'].".".$parsed_url['tld'];
        if($parsed_url['subdomain'] === "www"){
            return $parsed_url['parent_domain'];
        }else{
            return $parsed_url['domain'];
        }

    }

    function getHostFromDomain($domain){
        $subDomain = $domain;
        $level = explode(".",$domain);
        if(sizeof($level) > 2) {
            $subDomain = "";
            $counter = 0;
            foreach($level as $value){
                if($counter != 0){
                    $subDomain .= $value;
                    if($counter+1 != sizeof($level)){
                        $subDomain .= '.';
                    }
                }
                $counter++;
            }
        }
        return $subDomain;
    }


    /**
     *
     * @Route("/whitelist-target/{id}", options={"expose"=true}, name="_route")
     * @Post(path="/whitelist-target/{id}")
     * @ApiDoc(
     *     section="WhiteListTarget",
     *     description="allows or denies permission for the Domain of the Whitel,
     * ist-Target with {id}",
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

        $stateArgument = $state == 1 || $state == 3 ? '-dw' : '-db';
        $path = $_SERVER["DOCUMENT_ROOT"];
        $domain = $state == 3 ? $whiteListTarget->getDomain() : $this->getHostFromDomain($whiteListTarget->getDomain());
        $cmd = $path.'/plink.exe -pw abc123! -ssh "test@192.168.1.16" /home/test/freigeben.sh '.$stateArgument.' '.$domain;
        if($state == 3)
        {
            $host = $this->getHostFromDomain($whiteListTarget->getDomain());
            $cmd2 = $path.'/plink.exe -pw abc123! -ssh "test@192.168.1.16" /home/test/freigeben.sh -db '.$host;
            pclose(popen("start /B ". $cmd2, "r"));
        }
        pclose(popen("start /B ". $cmd, "r"));
        $this->get('doctrine.orm.default_entity_manager')->flush();
        return $this->view('OK');
    }

    /**
     * @Post("/proxy/restart",name="proxy.restart")
     * @ApiDoc(
     *     section="Proxy",
     *     description="Refreshes Proxy server configs"
     * )
     */
    public function restartProxyAction(Request $request)
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
        $path = $_SERVER["DOCUMENT_ROOT"];
        $cmd = $path.'/plink.exe -pw abc123! -ssh "test@192.168.1.16" /home/test/freigeben.sh -restart';
        pclose(popen("start /B ". $cmd, "r"));
        return $this->view('OK');
    }
}