<?php
/**
 * Created by PhpStorm.
 * User: 72498
 * Date: 25.07.2016
 * Time: 17:57
 */

namespace NWLBundle\Service;


class LDAPUser
{

    /**
     * @var bool
     */
    private $admin = false;

    private $firstname;

    private $lastname;

    /**
     * LDAPUser constructor.
     * @param bool $admin
     * @param $firstname
     * @param $lastname
     */
    public function __construct($firstname, $lastname, $admin = false)
    {
        $this->admin = $admin;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
    }


    /**
     * @return boolean
     */
    public function isAdmin()
    {
        return $this->admin;
    }

    /**
     * @param boolean $admin
     */
    public function setAdmin($admin)
    {
        $this->admin = $admin;
    }

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }


}