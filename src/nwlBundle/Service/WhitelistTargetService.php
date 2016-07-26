<?php
/**
 * Created by PhpStorm.
 * User: 72498
 * Date: 21.07.2016
 * Time: 09:37
 */

namespace nwlBundle\Service;


use Doctrine\ORM\EntityManagerInterface;
use nwlBundle\Entity\WhiteListTarget;

class WhitelistTargetService
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
     * @param $domain string
     * @return WhitelistTarget|object
     */
    public function getOrCreateTargetBy($domain)
    {
        $wltRepo = $this->em->getRepository('nwlBundle:WhitelistTarget');
        $target = $wltRepo->findOneBy(array('domain' => $domain));
        if (null == $target) {
            $target = new WhitelistTarget();
            $target->setDomain($domain);
            $this->em->persist($target);
            $this->em->flush();
        }
        return $target;
    }


}