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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ApiResource(
 *     collectionOperations={"GET"},
 *     itemOperations={"GET"},
 *     normalizationContext={"groups"={"work_of_art_read"}},
 * )
 * @ApiFilter(SearchFilter::class, properties={
 *     "name":"partial",
 *     "artist.id": "exact",
 *     "artist.name": "partial",
 *     "location.id": "exact",
 *     "location.name": "partial",
 *     "categories.id": "exact",
 *     "query": "partial"
 * })
 * @ORM\Entity(repositoryClass="App\Repository\WorkOfArtRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\Loggable()
 * @Vich\Uploadable()
 */
class WorkOfArt
{
    use TimestampableEntity;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     * @Groups("work_of_art_read")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     * @Groups("work_of_art_read")
     */
    protected $updatedAt;

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
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Versioned()
     * @Groups("work_of_art_read")
     */
    private $image;

    /**
     * @Vich\UploadableField(mapping="works_of_art", fileNameProperty="image")
     *
     * @var \Symfony\Component\HttpFoundation\File\File
     */
    private $imageFile;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Artist", inversedBy="works")
     * @Groups("work_of_art_read")
     */
    private $artist;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Location", inversedBy="works")
     * @Groups("work_of_art_read")
     */
    private $location;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Category", inversedBy="works")
     * @Groups("work_of_art_read")
     */
    private $categories;

    /**
     * @ORM\Column(type="string", length=2040, nullable=true)
     */
    private $query;

    /**
     * @ORM\Column(type="json")
     * @Groups("work_of_art_read")
     */
    private $images = [];

    public function __construct()
    {
        $this->categories = new ArrayCollection();
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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image = null): self
    {
        $this->image = $image;

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * @param File $imageFile
     *
     * @return WorkOfArt
     */
    public function setImageFile(File $imageFile = null): self
    {
        $this->imageFile = $imageFile;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($imageFile) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    public function getArtist(): ?Artist
    {
        return $this->artist;
    }

    public function setArtist(?Artist $artist): self
    {
        $this->artist = $artist;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name ?? self::class;
    }

    public function getQuery(): ?string
    {
        return $this->query;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function updateQuery()
    {
        $query[] = $this->name;
        if (null !== $this->getArtist()) {
            $query[] = $this->getArtist()->getName();
        }
        if (null !== $this->getLocation()) {
            $query[] = $this->getLocation()->getName();
        }

        $this->query = implode(' ', $query);
    }

    public function getImages(): ?array
    {
        return $this->images;
    }

    public function setImages(array $images): self
    {
        $this->images = $images;

        return $this;
    }
}
