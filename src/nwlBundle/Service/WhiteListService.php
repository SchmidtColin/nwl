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

    const AGE_NEW  = 0;
    const AGE_ALL  = 1;
    const AGE_OLD = 2;

    const PAST_THIRTY_DAYS = 30;

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
     * @param $age integer
     * @return \nwlBundle\DTO\WhiteListTargetDTO[]
     */
    public function findAllWhiteListTargets($age = self::AGE_ALL)
    {
        $targetDTOArray = array();
        switch ($age) {
            case self::AGE_NEW:
                $targets = $this->em->getRepository('nwlBundle:WhiteListTarget')->getByDate(self::PAST_THIRTY_DAYS);
                break;
            case self::AGE_OLD:
                $targets = $this->em->getRepository('nwlBundle:WhiteListTarget')->getOldByDate(self::PAST_THIRTY_DAYS);
                break;
            case self::AGE_ALL:
            Default:
                $targets = $this->em->getRepository('nwlBundle:WhiteListTarget')->findAll();
        }


        foreach($targets as $target)
        {
            $targetDTO = new WhiteListTargetDTO();
            $targetDTO->setId($target->getId());
            $targetDTO->setDomain($target->getDomain());
            $targetDTO->setState($target->getState());
            $targetDTO->setDecidedBy($target->getDecidedBy());
            $targetDTO->setDecisionDate($target->getDecisionDate());
            $criteria =array('whitelistTarget'=>$target);
            $requestArray = $this->em->getRepository('nwlBundle:WhiteListRequest')->findBy($criteria);
            $targetDTO->setWhitelistRequests($requestArray);
            array_push($targetDTOArray,$targetDTO);
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

    /**
     * @param WhiteListTarget $target
     * @return mixed
     */
    public function decideWhiteListTargetState($target)
    {
        $this->em->flush();
        return $target;
    }
}