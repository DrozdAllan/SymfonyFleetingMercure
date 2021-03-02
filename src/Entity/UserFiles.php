<?php

namespace App\Entity;

use App\Repository\UserFilesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserFilesRepository::class)
 */
class UserFiles
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="userFiles")
     */
    private $image;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage(): ?User
    {
        return $this->image;
    }

    public function setImage(?User $image): self
    {
        $this->image = $image;

        return $this;
    }
}
