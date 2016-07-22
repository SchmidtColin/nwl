<?php

namespace FrontEndBundle\Controller;

use nwlBundle\Entity\WhiteListRequest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/",name="whitelist-request.list.bla")
     */
    public function indexAction()
    {
        $params = array('username'=>'67827');
        return $this->render('FrontEndBundle:Default:index.html.twig',$params);
    }

    /**
     * @Route("/login", name="whitelist-request.login")
     */
    public function loginAction(){

    }

    /**
     * @Route("/whitelist-request/{username}", name="whitelist-request.list")
     * @param string $username
     */
    public function listAction($username){

    }

    /**
     * @Route("/whitelist-request/create", name="whitelist-request.form")
     */
    public function createFormAction()
    {


    }

}
