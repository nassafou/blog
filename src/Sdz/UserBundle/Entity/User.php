<?php

namespace Sdz\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * User
 *
 * @ORM\Table(name="sdz_user")
 * @ORM\Entity(repositoryClass="Sdz\UserBundle\Entity\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
}
