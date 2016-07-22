<?php
/**
 * Created by PhpStorm.
 * User: 67827
 * Date: 20.07.2016
 * Time: 11:59
 */

namespace nwlBundle\Controller;


use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends FOSRestController
{
    /**
     * @Get(path="/user")
     * @ApiDoc(
     *     section="User",
     *     description="Lists all Users")
     */
    public function listUsersAction()
    {
        $em = $this->get('doctrine.orm.default_entity_manager');
        $users = $em->getRepository('nwlBundle:User')->findAll();

        return $this->view($users);
    }
}