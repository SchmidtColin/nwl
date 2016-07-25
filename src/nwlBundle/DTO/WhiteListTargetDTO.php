<?php
/**
 * Created by PhpStorm.
 * User: 67521
 * Date: 25.07.2016
 * Time: 15:48
 */

namespace nwlBundle\DTO;


class WhiteListTargetDTO
{

    private $domain;

    private $state;

    private $whitelistRequests;

    /**
     * @return mixed
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param mixed $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return mixed
     */
    public function getWhitelistRequests()
    {
        return $this->whitelistRequests;
    }

    /**
     * @param mixed $whitelistRequests
     */
    public function setWhitelistRequests($whitelistRequests)
    {
        $this->whitelistRequests = $whitelistRequests;
    }



}