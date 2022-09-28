<?php

namespace App\Services;

use App\Entity\Author;
use App\Entity\Comment;
use App\Entity\Post;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use \Knp\Component\Pager\Pagination\PaginationInterface as Pagination;

const PAGE_NUMBER = 1;
const LIMIT_PER_PAGE = 4;

class PostService
{
    private ObjectRepository $posts;
    private ObjectRepository $comments;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->posts = $doctrine->getRepository(Post::class);
        $this->comments = $doctrine->getRepository(Comment::class);
    }

    public function getAllPosts(Request $request, PaginatorInterface $paginator): Pagination|false
    {
        $posts = $this->posts->findAll();

        if (!$posts) {
            return false;
        }

        return $paginator->paginate(
            $posts,
            $request->query->getInt('page', PAGE_NUMBER),
            LIMIT_PER_PAGE
        );
    }

    public function getPostComments(): array
    {
        try {
            return $this->comments->findAll();
        } catch (Exception $e) {
            return [];
        }
    }

    public function createPostForm(Request $request, ManagerRegistry $doctrine, $form) {
        $entityManager = $doctrine->getManager();

        // dummy data for non-registrated account
        $authors = $doctrine->getRepository(Author::class)->findAll();
        if (!$authors) {
            $author = new Author();
            $author->setName('dummy name')
                   ->setEmail('dummy@email.com');
            $entityManager->persist($author);
            $entityManager->flush();
        } else {
            $author = $authors[0];
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPost = $form->getData();
            $newPost->setCreated();
            $newPost->setAuthor($author);
            $entityManager->persist($newPost);
            $entityManager->flush();
        }

        try {
            return $form;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getPost($id)
    {
        $post = $this->posts->find($id);

        if (!$post) {
            throw $this->createNotFoundException(
                'No posts found for id '.$id
            );
        }

        return $post;
    }
}