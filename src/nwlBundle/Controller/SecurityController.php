<?php
/**
 * Created by PhpStorm.
 * User: 72498
 * Date: 25.07.2016
 * Time: 17:07
 */

namespace nwlBundle\Controller;


use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends FOSRestController
{
    /**
     * @Post("/auth")
     * @ApiDoc(
     *     section="Authentification",
     *     description="User can aquire a login Token for performing API requests",
     *     parameters={
     *          { "name"="username", "dataType"="string", "required"=true, "description"="Users username" },
     *          { "name"="password", "dataType"="string", "required"=true, "description"="Users password" }
     *     }
     * )
     */
    public function loginAction(Request $request)
    {
        $userService = $this->get('nwl.user');

        $username = $request->get('username');
        $password = $request->get('password');

        $user = $userService->generateLoginToken($username, $password);
        if ($user !== null && $user->getApikey() !== null) {
            return $this->view(array('token' => $user->getApikey()));
        } else {
            return $this->view(array('message' => 'Invalid Credentials'), 401);
        }
    }


}