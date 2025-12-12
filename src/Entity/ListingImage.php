<?php

namespace App\Entity;

use App\Repository\ListingImageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ListingImageRepository::class)]
class ListingImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $imagePath = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Listing $listing = null;

    public function getId(): ?int { return $this->id; }

    public function getImagePath(): ?string { return $this->imagePath; }
    public function setImagePath(string $imagePath): static {
        $this->imagePath = $imagePath;
        return $this;
    }

    public function getListing(): ?Listing { return $this->listing; }
    public function setListing(?Listing $listing): static {
        $this->listing = $listing;
        return $this;
    }
}
