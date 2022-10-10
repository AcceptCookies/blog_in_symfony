<?php

namespace App\Entity;

use App\Repository\PostRepository;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\PrePersist;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3)]
    private ?string $title = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3)]
    private ?string $description = null;

    #[ORM\Column(type: 'datetime')]
    private ?DateTime $created = null;

    #[ORM\Column(type: 'string', length: 5000)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3)]
    private ?string $content = null;

    #[ORM\ManyToOne(targetEntity: Author::class, inversedBy: 'post')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Author $author = null;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Comment::class, orphanRemoval: true)]
    #[ORM\JoinColumn(nullable: true)]
    private Comment|Collection $comment;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreated(): ?DateTime
    {
        return $this->created;
    }

    #[PrePersist]
    public function setCreated(): self
    {
        $this->created = new DateTime();

        return $this;

    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(?Author $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getComment(): Comment|Collection
    {
        return $this->comment;
    }

    public function setComment(Comment $comment): self
    {
        if ($comment->getPost() !== $this) {
            $comment->setPost($this);
        }

        $this->comment = $comment;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }
}
