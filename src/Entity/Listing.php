<?php
 namespace App\Entity;

use App\Repository\ListingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: ListingRepository::class)]
class Listing
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(type: Types::FLOAT)]
    private ?float $rentPrice = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateOfPublish = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $availableFrom = null;

    #[ORM\ManyToOne(targetEntity: Appartment::class, inversedBy: 'listings')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Appartment $appartment = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $owner = null;
    #[ORM\OneToMany(mappedBy: 'listing', targetEntity: ListingImage::class, cascade: ['persist', 'remove'])]
private Collection $images;

public function __construct()
{
    $this->images = new ArrayCollection();
}

public function getImages(): Collection
{
    return $this->images;
}

public function addImage(ListingImage $image): static
{
    if (!$this->images->contains($image)) {
        $this->images->add($image);
        $image->setListing($this);
    }

    return $this;
}

public function removeImage(ListingImage $image): static
{
    if ($this->images->removeElement($image)) {
        if ($image->getListing() === $this) {
            $image->setListing(null);
        }
    }

    return $this;
}


    // Getters and setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;
        return $this;
    }

    public function getRentPrice(): ?float
    {
        return $this->rentPrice;
    }

    public function setRentPrice(float $rentPrice): static
    {
        $this->rentPrice = $rentPrice;
        return $this;
    }

    public function getDateOfPublish(): ?\DateTimeInterface
    {
        return $this->dateOfPublish;
    }

  
    public function setDateOfPublish(?\DateTimeInterface $dateOfPublish): static
    {
        $this->dateOfPublish = $dateOfPublish;
        return $this;
    }
    
    
    public function getAvailableFrom(): ?\DateTimeInterface
    {
        return $this->availableFrom;
    }

    public function setAvailableFrom(\DateTimeInterface $availableFrom): static
    {
        $this->availableFrom = $availableFrom;
        return $this;
    }
    public function getAppartment(): ?Appartment
    {
        return $this->appartment;
    }

    public function setAppartment(?Appartment $appartment): self
    {
        $this->appartment = $appartment;
        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): static
    {
        $this->owner = $owner;
        return $this;
    }
    // In your Listing entity
/**
 * @ORM\Column(type="datetime")
 */

}
