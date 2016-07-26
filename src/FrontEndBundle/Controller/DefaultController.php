<?php

namespace FrontEndBundle\Controller;

use nwlBundle\Entity\WhiteListRequest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/login", name="whitelist-request.login")
     * @return Response
     * @internal param Request $request
     */
    public function loginAction()
    {
        return $this->render('FrontEndBundle:Default:login.html.twig');
    }

    /**
     * @Route("/whitelist-request/{username}", name="whitelist-request.list")
     * @param string $username
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction($username)
    {
        $userService = $this->get('nwl.user');
        $user = $userService->getById($username);

        $template = null ;
        $params = array('username'=>$username);
        $isAdminAuth = $user->getAdmin();
        if($isAdminAuth){
            $template = 'FrontEndBundle:Default:adminList.html.twig';
        }else{
            $template = 'FrontEndBundle:Default:userList.html.twig';
        }
        return $this->render($template, $params);
    }

    /**
     * @Route("/whitelist-request/{username}/create", name="whitelist-request.form")
     * @param Request $request
     * @param $username
     * @return Response
     */
    public function createFormAction(Request $request, $username)
    {
        $whitelistRequest = new WhiteListRequest();
        $domain = $request->request->get('domain');
        $reason = $request->request->get('reason');


        if ($domain != null && $reason != null) {
            $userService = $this->get('nwl.user');
            $whitelistService = $this->get('nwl.whitelist');

            $user = $userService->getById($username);
            $target = $whitelistService->getOrCreateTargetByDomain($domain);

            $whitelistRequest->setUser($user);
            $whitelistRequest->setWhitelistTarget($target);
            $whitelistRequest->setReason($reason);
            $whitelistRequest->setCreated(new \DateTime());
            $whitelistService->createWhiteListRequest($whitelistRequest);

            $template = null;
            $params = array('username' => $username);
            $isAdminAuth = $user->getAdmin();
            if ($isAdminAuth) {
                $template = 'FrontEndBundle:Default:adminList.html.twig';
            } else {
                $template = 'FrontEndBundle:Default:userList.html.twig';
            }
            return $this->render($template, $params);
        }

        return $this->render('FrontEndBundle:Default:requestform.html.twig', array('username' => $username));
    }


}
