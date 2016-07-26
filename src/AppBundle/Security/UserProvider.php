<?php
/**
 * Created by PhpStorm.
 * User: 72498
 * Date: 25.07.2016
 * Time: 15:04
 */

namespace AppBundle\Security;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;


    /**
     * UserProvider constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

    }

    public function getUsernameForApiKey($apiKey)
    {
        // Look up the username based on the token in the database, via
        // an API call, or do something entirely different
        $username = '';
        $user = $this->em->getRepository('NWLBundle:User')->findOneBy(array('apikey' => $apiKey));
        if ($user !== null) {
            return $user->getUsername();
        }
        return null;
    }

    public function loadUserByUsername($username)
    {

        $user = $this->em->find('NWLBundle:User', $username);
        return $user;
//        return new User(
//            $username,
//            null,
//            // the roles for the user - you may choose to determine
//            // these dynamically somehow based on the user
//            array('ROLE_API')
//        );
    }

    public function refreshUser(UserInterface $user)
    {
        // this is used for storing authentication in the session
        // but in this example, the token is sent in each request,
        // so authentication can be stateless. Throwing this exception
        // is proper to make things stateless
        throw new UnsupportedUserException();
    }

    public function supportsClass($class)
    {
        return 'NWLBundle\Entity\User' === $class;
    }
}