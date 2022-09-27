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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

    public function getAllPosts(Request $request, PaginatorInterface $paginator): \Knp\Component\Pager\Pagination\PaginationInterface
    {
        $posts = $this->posts->findAll();

        if (!$posts) {
            throw new NotFoundHttpException(
                'No posts found'
            );
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
        $authors = $doctrine->getRepository(Author::class)->findAll();
        // dummy data for non-registrated account
        if (!$authors) {
            throw new NotFoundHttpException(
                'No author found'
            );
        }

        $fakeAuthor = $authors[0];
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPost = $form->getData();
            $newPost->setCreated();
            $newPost->setAuthor($fakeAuthor);
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