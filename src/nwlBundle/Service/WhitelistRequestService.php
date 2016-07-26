<?php
/**
 * Created by PhpStorm.
 * User: 72498
 * Date: 21.07.2016
 * Time: 10:23
 */

namespace NWLBundle\Service;


use Doctrine\ORM\EntityManagerInterface;
use NWLBundle\Entity\User;
use NWLBundle\Entity\WhitelistRequest;
use NWLBundle\Entity\WhitelistTarget;

class WhitelistRequestService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * UserService constructor.
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param $whitelistRequest WhitelistRequest
     * @return WhitelistRequest
     */
    public function createWhitelistRequest($whitelistRequest)
    {
        $this->em->persist($whitelistRequest);
        $this->em->flush();
        return $whitelistRequest;
    }

    /**
     * @param $target WhitelistTarget
     * @param $user User
     *
     * @return WhitelistRequest|object
     */
    public function findWhitelistRequest($target, $user)
    {
        $wlrRepo = $this->em->getRepository('NWLBundle:WhitelistRequest');
        return $wlrRepo->findOneBy(array('whitelistTarget' => $target, 'user' => $user));
    }

    /**
     * @param $user User
     * @return array|\NWLBundle\Entity\WhitelistRequest[]
     */
    public function findRequestsBy($user)
    {
        return $this->em->getRepository('NWLBundle:WhitelistRequest')->findBy(array('user' => $user));
    }

}