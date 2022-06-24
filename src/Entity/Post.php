<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\PrePersist;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTime $created = null;

    #[ORM\Column(type: 'string', length: 65535)]
    private ?string $content = null;

    #[ORM\ManyToOne(targetEntity: Author::class, inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Author $author = null;

    #[ORM\OneToMany(mappedBy: 'posts', targetEntity: Comment::class, orphanRemoval: true)]
    private Comment|Collection $comment;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreated(): ?\DateTime
    {
        return $this->created;
    }

    #[PrePersist]
    public function setCreated(): self
    {
        $this->created = new \DateTime();

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

    public function getComment(): ?Comment
    {
        return $this->comment;
    }

    public function setComment(Comment $comment): self
    {
        // set the owning side of the relation if necessary
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
