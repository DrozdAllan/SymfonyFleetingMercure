<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
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
     * @Groups("chatmessage")
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
    private $announcer;

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
     * @ORM\OneToMany(targetEntity=Image::class, mappedBy="user", orphanRemoval=true)
     */
    private $images;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="user", orphanRemoval=true)
     */
    private $messages;

    /**
     * @ORM\ManyToMany(targetEntity=Channel::class, mappedBy="Users")
     */
    private $channels;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mail;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $vip;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $stripe;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $notif;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $eyes;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nationality;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $language;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->channels = new ArrayCollection();
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
        $this->plainPassword = null;
    }

    public function getAnnouncer(): ?bool
    {
        return $this->announcer;
    }

    public function setAnnouncer(bool $announcer): self
    {
        $this->announcer = $announcer;

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
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setUser($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getUser() === $this) {
                $image->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setUser($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getUser() === $this) {
                $message->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Channel[]
     */
    public function getChannels(): Collection
    {
        return $this->channels;
    }

    public function addChannel(Channel $channel): self
    {
        if (!$this->channels->contains($channel)) {
            $this->channels[] = $channel;
            $channel->addUser($this);
        }

        return $this;
    }

    public function removeChannel(Channel $channel): self
    {
        if ($this->channels->removeElement($channel)) {
            $channel->removeUser($this);
        }

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getVip(): ?\DateTime
    {
        return $this->vip;
    }

    public function setVip(?\DateTime $vip): self
    {
        $this->vip = clone $vip;

        return $this;
    }

    public function getStripe(): ?string
    {
        return $this->stripe;
    }

    public function setStripe(?string $stripe): self
    {
        $this->stripe = $stripe;

        return $this;
    }

    public function getNotif(): ?bool
    {
        return $this->notif;
    }

    public function setNotif(?bool $notif): self
    {
        $this->notif = $notif;

        return $this;
    }

    public function getEyes(): ?string
    {
        return $this->eyes;
    }

    public function setEyes(string $eyes): self
    {
        $this->eyes = $eyes;

        return $this;
    }

    public function getNationality(): ?string
    {
        return $this->nationality;
    }

    public function setNationality(string $nationality): self
    {
        $this->nationality = $nationality;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }
}
