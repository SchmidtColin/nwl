<?php
/**
 * Created by PhpStorm.
 * User: 67827
 * Date: 20.07.2016
 * Time: 10:58
 */

namespace nwlBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class WhiteListRequest
 * @package nwlBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="whitelist_target")
 */
class WhiteListTarget
{
    const STATE_PENDING  = 0;
    const STATE_ALLOW    = 1;
    const STATE_DENY     = 2;
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="domain", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $domain;

    /**
     * @var int
     *
     * @ORM\Column(name="state", type="integer")
     */
    private $state = self::STATE_PENDING;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="nwlBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true, referencedColumnName="username")
     */
    private $decidedBy;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param int $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return User
     */
    public function getDecidedBy()
    {
        return $this->decidedBy;
    }

    /**
     * @param User $decidedBy
     */
    public function setDecidedBy($decidedBy)
    {
        $this->decidedBy = $decidedBy;
    }

}