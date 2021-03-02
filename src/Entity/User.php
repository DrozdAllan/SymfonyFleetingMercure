<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @UniqueEntity(fields={"username"}, message="There is already an account with this username")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="boolean")
     */
    private $Announcer;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $hair;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $tattoo;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $smoke;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $shortdescription;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $validadmin;

    /**
     * @ORM\OneToMany(targetEntity=Media::class, mappedBy="user")
     */
    private $mediaId;

    public function __construct()
    {
        $this->mediaId = new ArrayCollection();
    }



    

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getAnnouncer(): ?bool
    {
        return $this->Announcer;
    }

    public function setAnnouncer(bool $Announcer): self
    {
        $this->Announcer = $Announcer;

        return $this;
    }

    public function getHair(): ?string
    {
        return $this->hair;
    }

    public function setHair(?string $hair): self
    {
        $this->hair = $hair;

        return $this;
    }

    public function getTattoo(): ?bool
    {
        return $this->tattoo;
    }

    public function setTattoo(?bool $tattoo): self
    {
        $this->tattoo = $tattoo;

        return $this;
    }

    public function getSmoke(): ?bool
    {
        return $this->smoke;
    }

    public function setSmoke(?bool $smoke): self
    {
        $this->smoke = $smoke;

        return $this;
    }

    public function getShortdescription(): ?string
    {
        return $this->shortdescription;
    }

    public function setShortdescription(?string $shortdescription): self
    {
        $this->shortdescription = $shortdescription;

        return $this;
    }

    public function getValidadmin(): ?bool
    {
        return $this->validadmin;
    }

    public function setValidadmin(?bool $validadmin): self
    {
        $this->validadmin = $validadmin;

        return $this;
    }

    /**
     * @return Collection|Media[]
     */
    public function getMediaId(): Collection
    {
        return $this->mediaId;
    }

    public function addMediaId(Media $mediaId): self
    {
        if (!$this->mediaId->contains($mediaId)) {
            $this->mediaId[] = $mediaId;
            $mediaId->setUser($this);
        }

        return $this;
    }

    public function removeMediaId(Media $mediaId): self
    {
        if ($this->mediaId->removeElement($mediaId)) {
            // set the owning side to null (unless already changed)
            if ($mediaId->getUser() === $this) {
                $mediaId->setUser(null);
            }
        }

        return $this;
    }
}
