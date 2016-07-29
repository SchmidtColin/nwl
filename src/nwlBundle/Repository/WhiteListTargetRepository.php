<?php
/**
 * Created by PhpStorm.
 * User: 67520
 * Date: 28.07.2016
 * Time: 14:37
 */

namespace nwlBundle\Repository;


use Doctrine\ORM\EntityRepository;
use nwlBundle\Entity\WhiteListTarget;

class WhiteListTargetRepository extends EntityRepository
{
    /**
     * @param $amountDays integer
     * @return array
     */
    public function getByDate($amountDays)
    {
        $now   = new \DateTime();
        $pastThirtyDays = (new \DateTime())->modify('-'.$amountDays.' days');

        $qb = $this->createQueryBuilder("e");
        $qb
            ->andWhere('e.decisionDate BETWEEN :pastThirtyDays AND :now')
            ->orWhere('e.state = '.WhiteListTarget::STATE_PENDING)
            ->setParameter('pastThirtyDays', $pastThirtyDays )
            ->setParameter('now', $now)
        ;
        $result = $qb->getQuery()->getResult();

        return $result;
    }

    public function getOldByDate($amountDays)
    {
        $pastThirtyDays = (new \DateTime())->modify('-'.$amountDays.' days');

        $qb = $this->createQueryBuilder("e");
        $qb
            ->andWhere('e.decisionDate <= :pastThirtyDays')
            ->andWhere('e.state != '.WhiteListTarget::STATE_PENDING)
            ->setParameter('pastThirtyDays', $pastThirtyDays )
        ;
        $result = $qb->getQuery()->getResult();



        return $result;
    }

}



