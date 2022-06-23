<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

const PAGE_NUMBER = 1;
const LIMIT_PER_PAGE = 4;

class PostController extends AbstractController
{
    #[Route('/posts', name: 'posts')]
    public function overview(ManagerRegistry $doctrine, PaginatorInterface $paginator, Request $request): Response
    {
        $posts = $doctrine->getRepository(Post::class)->findAll();
        $comments  = $doctrine->getRepository(Comment::class)->findAll();

        if (!$posts) {
            throw $this->createNotFoundException(
                'No post found'
            );
        }

        $pagination = $paginator->paginate(
            $posts,
            $request->query->getInt('page', PAGE_NUMBER),
            LIMIT_PER_PAGE
        );

        return $this->render('post/overview.html.twig', [
            'comments' => $comments,
            'pagination' => $pagination
        ]);
    }
    
    #[Route('/post/{id}', name: 'post')]
    public function detail(ManagerRegistry $doctrine, int $id): Response
    {
        $post = $doctrine->getRepository(Post::class)->find($id);
        $comments  = $doctrine->getRepository(Comment::class)->findAll();

        if (!$post) {
            throw $this->createNotFoundException(
                'No post found for id '.$id
            );
        }

        return $this->render('post/detail.html.twig', [
            'post' => $post,
            'comments' => $comments
        ]);
    }
}
