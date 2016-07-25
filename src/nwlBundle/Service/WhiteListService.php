<?php
/**
 * Created by PhpStorm.
 * User: 67827
 * Date: 20.07.2016
 * Time: 14:00
 */

namespace nwlBundle\Service;


use Doctrine\ORM\EntityManagerInterface;
use nwlBundle\Entity\User;
use nwlBundle\Entity\WhiteListRequest;
use nwlBundle\Entity\WhiteListTarget;
use nwlBundle\DTO\WhiteListTargetDTO;

class WhiteListService
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
     * @return WhiteListTargetDTO[]
     */
    public function findAllWhiteListTargets()
    {
        $targetDTOArray = array();
        $targets = $this->em->getRepository('nwlBundle:WhiteListTarget')->findAll();
        foreach($targets as $target)
        {
            $dto = new WhiteListTargetDTO();
            $dto->setDomain($target->getDomain());
            $dto->setState($target->getState());
            $criteria =array('whitelistTarget'=>$target);
            $requestArray = $this->em->getRepository('nwlBundle:WhiteListRequest')->findBy($criteria);
            $dto->setWhitelistRequests($requestArray);
        }
        return $targetDTOArray;
    }

    /**
     * @return array|\nwlBundle\Entity\WhiteListRequest[]
     */
    public function findAllWhiteListRequests()
    {
        return $this->em->getRepository('nwlBundle:WhiteListRequest')->findAll();
    }

    /**
     * @param $user string
     * @return WhiteListRequest[]
     */
    public function findWhiteListRequestsByUsername($user)
    {
       return $this->em->getRepository('nwlBundle:WhiteListRequest')->findBy(array('user' => $user));
    }

    /**
     * @param WhiteListTarget $target
     * @param User $user
     * @return null | WhiteListRequest
     */
    public function findWhiteListRequest($target, $user)
    {
        $requestRepo = $this->em->getRepository('nwlBundle:WhiteListRequest');
        return $requestRepo->findOneBy(array('whitelistTarget' => $target,'user'=>$user));
    }

    /**
     * @param string $domain
     * @return WhiteListTarget
     */
    public function getOrCreateTargetByDomain($domain)
    {
        $whiteListTargetRepo = $this->em->getRepository('nwlBundle:WhiteListTarget');
        $target = $whiteListTargetRepo->findOneBy(array('domain' => $domain));
        if(null === $target){
            $target = new WhiteListTarget();
            $target->setDomain($domain);
            $this->em->persist($target);
            $this->em->flush();
        }
        return $target;
    }

    /**
     * @param WhiteListRequest $whiteListRequest
     * @return WhiteListRequest
     */
    public function createWhiteListRequest($whiteListRequest)
    {
        $this->em->persist($whiteListRequest);
        $this->em->flush();
        return $whiteListRequest;
    }

    public function decideWhiteListTargetState($target){

    }
}