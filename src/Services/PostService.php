<?php

namespace App\Services;

use App\Entity\Author;
use App\Entity\Post;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use \Knp\Component\Pager\Pagination\PaginationInterface as Pagination;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

const PAGE_NUMBER = 1;
const LIMIT_PER_PAGE = 4;

class PostService
{
    private ObjectRepository $posts;
    private ObjectRepository $author;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->posts = $doctrine->getRepository(Post::class);
        $this->author = $doctrine->getRepository(Author::class);
    }

    public function getAllPosts(Request $request, PaginatorInterface $paginator): Pagination
    {
        $data = $this->posts->
        getPaginatedPosts($request->query->getInt('page', PAGE_NUMBER), LIMIT_PER_PAGE);
        $pagination = $paginator->paginate(
            $data['posts'],
            $request->query->getInt('page', PAGE_NUMBER),
            LIMIT_PER_PAGE
        );
        $pagination->setItems($data['posts']);
        $pagination->setTotalItemCount($data['posts_count']);

        return $pagination;
    }

    public function createPostForm(Request $request, ManagerRegistry $doctrine, $form) {
        $entityManager = $doctrine->getManager();
        $author = $this->author->getFirstOrCreate();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPost = $form->getData();
            $newPost->setCreated();
            $newPost->setAuthor($author);
            $entityManager->persist($newPost);
            $entityManager->flush();
        }

        return $form;
    }

    public function getPost($id): object
    {
        $post = $this->posts->find($id);

        if (!$post) {
            throw new NotFoundHttpException();
        }

        return $post;
    }
}