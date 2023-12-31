<?php

namespace App\Entity;

use App\Repository\UrlMappingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UrlMappingRepository::class)]
#[ORM\Table(name:"url_mappings")]
class UrlMapping
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"AUTO")]
    #[ORM\Column]
    #[Groups(['api'])]
    private ?int $id = null;

    #[Groups(['api'])]
    #[ORM\Column(length: 255)]
    private ?string $longUrl = null;
    
    #[Groups(['api'])]
    #[ORM\Column(length: 10,unique:true)]
    private ?string $shortCode = null;

    #[Groups(['api'])]
    #[ORM\Column(nullable:true)]
    private ?int $clickCount = 0;

    #[ORM\ManyToOne(inversedBy: 'UrlMapping')]
    private ?User $client = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLongUrl(): ?string
    {
        return $this->longUrl;
    }

    public function setLongUrl(string $longUrl): static
    {
        $this->longUrl = $longUrl;

        return $this;
    }

    public function getShortCode(): ?string
    {
        return $this->shortCode;
    }

    public function setShortCode(string $shortCode): static
    {
        $this->shortCode = $shortCode;

        return $this;
    }

    public function getClickCount(): ?int
    {
        return $this->clickCount;
    }

    public function setClickCount(int $clickCount): static
    {
        $this->clickCount = $clickCount;

        return $this;
    }

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): static
    {
        $this->client = $client;

        return $this;
    }
}
