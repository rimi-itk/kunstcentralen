<?php

/*
 * This file is part of itk-dev/kunstcentralen.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ApiResource(
 *     collectionOperations={"GET"},
 *     itemOperations={"GET"}
 * )
 * @ApiFilter(SearchFilter::class, properties={"name":"partial"})
 * @ORM\Entity(repositoryClass="App\Repository\ArtistRepository")
 * @Gedmo\Loggable()
 */
class Artist
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Versioned()
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\WorkOfArt", mappedBy="artist")
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
            $work->setArtist($this);
        }

        return $this;
    }

    public function removeWork(WorkOfArt $work): self
    {
        if ($this->works->contains($work)) {
            $this->works->removeElement($work);
            // set the owning side to null (unless already changed)
            if ($work->getArtist() === $this) {
                $work->setArtist(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name ?? self::class;
    }
}
