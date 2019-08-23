<?php

/*
 * This file is part of itk-dev/kunstcentralen.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     collectionOperations={"GET"},
 *     itemOperations={"GET"}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 * @Gedmo\Loggable()
 */
class Category
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("work_of_art_read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Versioned()
     * @Groups("work_of_art_read")
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\WorkOfArt", mappedBy="categories")
     */
    private $works;

    public function __construct()
    {
        $this->works = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|WorkOfArt[]
     */
    public function getWorks(): Collection
    {
        return $this->works;
    }

    public function addWork(WorkOfArt $work): self
    {
        if (!$this->works->contains($work)) {
            $this->works[] = $work;
            $work->addCategory($this);
        }

        return $this;
    }

    public function removeWork(WorkOfArt $work): self
    {
        if ($this->works->contains($work)) {
            $this->works->removeElement($work);
            $work->removeCategory($this);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name ?? self::class;
    }
}
