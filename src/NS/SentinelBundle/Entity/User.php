<?php

namespace NS\SentinelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use \Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="NS\SentinelBundle\Repository\User")
 */
class User implements AdvancedUserInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=255)
     */
    private $salt;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @Assert\Length(
     *              min = "6",
     *              minMessage = "Your password must be at least 6 characters")
     *
     * @var type
     */
    protected $plainPassword;

    /**
     *
     * @var ACL $acls
     * 
     * @ORM\OneToMany(targetEntity="ACL", mappedBy="user", fetch="EAGER",cascade={"persist"}, orphanRemoval=true)
     * @ORM\JoinColumn(name="id",referencedColumnName="user_id")
     */
    protected $acls;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="isAdmin", type="boolean")
     */
    protected $isAdmin;
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    
        return $this;
    }

    /**
     * Get salt
     *
     * @return string 
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;
    
        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    
        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function resetSalt()
    {
        $this->salt = User::_resetSalt(array($this->name,$this->email));
    }

    static public function _resetSalt($fields = array())
    {
        return sha1(implode("",$fields).microtime());
    }

    public function eraseCredentials()
    {

    }

    public function getRoles()
    {
        $roles = array('ROLE_USER');

        // what happens if this returns null??
        foreach($this->acls as $acl)
            $roles = array_merge($roles,$acl->getType()->getAsCredential());

        return array_unique($roles);
    }

    public function getUsername() {
        return $this->email;
    }

    public function isAccountNonExpired() {
        return true;
    }

    public function isAccountNonLocked() {
        return true;
    }

    public function isCredentialsNonExpired() {
        return true;
    }

    public function isEnabled() {
        return true;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->acls = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add acls
     *
     * @param \NS\SentinelBundle\Entity\ACL $acls
     * @return User
     */
    public function addAcl(\NS\SentinelBundle\Entity\ACL $acls)
    {
        $this->acls[] = $acls;
    
        return $this;
    }

    /**
     * Remove acls
     *
     * @param \NS\SentinelBundle\Entity\ACL $acls
     */
    public function removeAcl(\NS\SentinelBundle\Entity\ACL $acls)
    {
        $this->acls->removeElement($acls);
    }

    /**
     * Get acls
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAcls()
    {
        return $this->acls;
    }

    /**
     * Set isAdmin
     *
     * @param boolean $isAdmin
     * @return User
     */
    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;
    
        return $this;
    }

    /**
     * Get isAdmin
     *
     * @return boolean 
     */
    public function getIsAdmin()
    {
        return $this->isAdmin;
    }
}