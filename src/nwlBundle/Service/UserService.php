<?php
/**
 * Created by PhpStorm.
 * User: 67827
 * Date: 20.07.2016
 * Time: 13:59
 */

namespace nwlBundle\Service;


use Doctrine\ORM\EntityManagerInterface;
use nwlBundle\Entity\User;

class UserService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * UserController constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param string $id
     * @return null | User
     */
    public function getById($id)
    {
        return $this->em->getRepository('nwlBundle:User')->find($id);
    }

    public function getOrCreateUserByUsername($username)
    {

        $userRepo = $this->em->getRepository('nwlBundle:User');
        $target = $userRepo->findOneBy(array('username' => $username));
        if (null === $target) {
            $target = new User();
            $target->setUsername($username);
            $target->setFirstname("Guest");
            $target->setLastname("Guest");
            $target->setAdmin(false);
            $this->em->persist($target);
            $this->em->flush();
        }
        return $target;

    }


    /**
     * @param $userid string
     * @return null|\NWLBundle\Entity\User|object
     */
    public function getUser($userid)
    {
        return $this->em->getRepository('NWLBundle:User')->find($userid);
    }

    /**
     * @param $username
     * @param $password
     * @return User
     */
    public function generateLoginToken($username, $password)
    {
        $userRepository = $this->em->getRepository('NWLBundle:User');


        $ldapUser = $this->authenticate($username, $password);
        if ($ldapUser !== null) {
            $user = $userRepository->find($username);
            $userAlreadyExists = true;
            if ($user === null) {
                $user = new User();
                $user->setUsername($username);
                $userAlreadyExists = false;
            }

            $user->setApikey(hash('sha512', $username . $password . bin2hex(openssl_random_pseudo_bytes(10))));
            $user->setFirstname($ldapUser->getFirstname());
            $user->setLastname($ldapUser->getLastname());
            $user->setAdmin($ldapUser->isAdmin());

            if (!$userAlreadyExists) {
                $this->em->persist($user);
            }
            $this->em->flush();
            return $user;

        }
        return null;
    }

    /**
     * @param $user
     * @param $password
     * @return null|LDAPUser
     */
    private function authenticate($user, $password)
    {
        // LDAP debugging
        ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7);

        // AD/LDAP server (IP or DNS Name)
        //$ldap_host = "ldaps://192.168.1.1:636";

        // AD/LDAP server over SSL (IP or DNS Name)
        // Warning! You musst use a Certificate and configure your LDAP-Server to use SSL
        $ldap_host = "ldap://192.168.1.1:389";

        // Active Directory DN
        $ldap_dn = "OU=Nestecs,DC=nestecs,DC=drv";

        // Active Directory user group
        $member = "InternetRestricted";
        $ldap_manager_group = "=" . $member . ",";

        $admin = "Verwaltung";
        $ldap_admin_group = "=" . $admin . ",";

        // User Domain
        $ldap_user_dom = "@nestecs.drv";

        // connect to active directory
        $ldap = ldap_connect($ldap_host) or die("couldn´t connect");

        $isUser = false;
        $isAdmin = false;

        // verify user and password
        error_reporting(E_ALL ^ E_WARNING);
        if ($bind = ldap_bind($ldap, $user . $ldap_user_dom, $password)) {
            // if valid -> get groups

            $filter = "(sAMAccountName=" . $user . ")";
            $attr = array("memberof", "givenName", "sn");
            $result = ldap_search($ldap, $ldap_dn, $filter, $attr) or exit("Unable to search in LDAP server");
            $entries = ldap_get_entries($ldap, $result);

            //var_dump($entries);
            // ldap bind disconnect

            ldap_unbind($ldap);

            // check groups
            foreach ($entries[0]['memberof'] as $grps) {
                // if is user is in group, then break the loop

                if (strpos($grps, $ldap_manager_group)) {
                    $isUser = true;
                }
                if (strpos($grps, $ldap_admin_group)) {
                    $isAdmin = true;
                }
            }

            if ($isUser || $isAdmin) {
                // user is in group and has rights
                //echo "user " . $user . " is in group " . $member;

                $lastname = "";
                $firstname = "";
                if ($result != false) {
                    foreach ($entries as $entry) {
                        if (isset($entry["sn"][0]) != false) {
                            //echo " and the lastname is: " . $entry["sn"][0];
                            //$lastname;
                            $lastname = $entry["sn"][0];
//                            $_SESSION['lastname'] = $lastname;
                        }
                        if (isset($entry["givenname"][0]) != false) {
                            //echo " and the firstname is: " . $entry["cn"][0];

                            $firstname = $entry["givenname"][0];
//                            $_SESSION['firstname'] = $firstname;
                        }
                    }
                }

                $ldapUser = new LDAPUser($firstname, $lastname, $isAdmin);
                return $ldapUser;
            } else {
                // user is not in group and has no rights
                //echo "user " . $user . " has no rights";
                return null;
            }
        } else {
            // name or password incorrect
            //echo "invalid name or password";
            return null;
        }
    }

}