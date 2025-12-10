<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Location;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;


    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'userId', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?User $userId = null;

    #[ORM\ManyToOne(targetEntity: Listing::class)]
    #[ORM\JoinColumn(name: 'listingId', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Listing $listingId = null;  // Correct type is Listing, not string


    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phoneNumber = null;

    
 
    

    public function getBookingId(): ?int
    {
        return $this->bookingId;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setBookingId(int $bookingId): static
    {
        $this->bookingId = $bookingId;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

// In Booking.php (Booking Entity)
public function getUserId(): ?User
{
    return $this->userId;
}

public function setUserId(User $userId): static
{
    $this->userId = $userId;

    return $this;
}

// In Booking.php (Booking Entity)
public function getListingId(): ?Listing
{
    return $this->listingId;
}

public function setListingId(Listing $listingId): static
{
    $this->listingId = $listingId;

    return $this;
}


    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }
}
