<?php

namespace FrontEndBundle\Controller;

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
     * @Route("/whitelist-request/{username}/create", name="whitelist-request.form")
     */
    public function createFormAction($username)
    {
        return $this->render('FrontEndBundle:Default:requestform.html.twig', array('username'=>$username));
    }

}
