<?php
/**
 * Created by PhpStorm.
 * User: 67827
 * Date: 20.07.2016
 * Time: 11:08
 */

namespace nwlBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class WhiteListRequest
 * @package nwlBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="whitelist_request")
 */
class WhiteListRequest
{

    /**
     * @var WhiteListTarget
     *
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="nwlBundle\Entity\WhiteListTarget")
     *
     * @Assert\NotNull()
     *
     */
    private $whitelistTarget;

    /**
     * @var User
     *
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="nwlBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false, referencedColumnName="username")
     *
     * @Assert\NotNull()
     *
     * @Serializer\Exclude()
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="reason", type="text")
     *
     * @Assert\NotBlank()
     * @Assert\Length(max="150")
     */
    private $reason;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     *
     * @Assert\NotNull()
     */
    private $created;


    /**
     * @return WhiteListTarget
     */
    public function getWhitelistTarget()
    {
        return $this->whitelistTarget;
    }

    /**
     * @param WhiteListTarget $whitelistTarget
     */
    public function setWhitelistTarget($whitelistTarget)
    {
        $this->whitelistTarget = $whitelistTarget;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @param string $reason
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }


}