<?php

namespace FrontEndBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/login", name="whitelist-request.login")
     */
    public function loginAction(){
        return $this->render('FrontEndBundle:Default:login.html.twig');
    }

    /**
     * @Route("/whitelist-request/{username}", name="whitelist-request.list")
     * @param string $username
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction($username){
        $params = array('username'=>$username);
        $template = null ;
        if($username != null){ //check if user or admin
            $template = 'FrontEndBundle:Default:userList.html.twig';
        }else{
            $template = 'FrontEndBundle:Default:adminList.html.twig';
        }

        return $this->render($template, $params);
    }

    /**
     * @Get()
     * @ApiDoc(
     *     section="f",
     *      description="d"
     * )
     * @Route("/whitelist-request/{username}/create", name="whitelist-request.form")
     * @param $username
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @internal param Request $request
     */
    public function createFormAction(Request $request, $username)
    {
        return $this->render('FrontEndBundle:Default:requestform.html.twig', array('username'=>$username));
    }

    /**
     * @Get()
     *      * @ApiDoc(
     *     section="f",
     *      description="d"
     * )
     *  @Route("/foo", name="whitelist-request.list.foo")
     */
    public function fooAction(Request $request){
        if($request->getMethod('POST')){
            $domain = $request->request->get('domain');
            $reason = $request->request->get('reason');
        }
        return new Response($domain . " " . $reason);
    }

}
