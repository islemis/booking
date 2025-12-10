<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Location;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(type: Types::BIGINT)]
    private ?string $rating = null;

    #[ORM\Column(length: 255)]
    private ?string $comment = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'userId', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?User $userId = null;
    
    #[ORM\ManyToOne(targetEntity: Listing::class)]
    #[ORM\JoinColumn(name: 'listingId', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Listing $listingId = null;


    public function getId(): ?int
    {
        return $this->id;
    }
    


    public function getRating(): ?string
    {
        return $this->rating;
    }

    public function setRating(string $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

 // Review.php


// Getters and Setters

public function getUserId(): ?User
{
    return $this->userId;
}

public function setUserId(User $user): self
{
    $this->userId = $user;

    return $this;
}

public function getListingId(): ?Listing
{
    return $this->listingId;
}

public function setListingId(?Listing $listingId): static
{
    $this->listingId = $listingId;

    return $this;
}

}
