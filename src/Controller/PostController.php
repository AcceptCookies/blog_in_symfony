<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/', name: 'app_post')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $posts = $doctrine->getRepository(Post::class)->findAll();
        $comments = $doctrine->getRepository(Comment::class)->findAll();

        if (!$posts) {
            throw $this->createNotFoundException(
                'No post found'
            );
        }

        return $this->render('base.html.twig', [
            'posts' => $posts,
            'comments' => $comments,
        ]);
    }

    /**
     * @Route("/post/{id}", name="post")
     */
    public function post(ManagerRegistry $doctrine, int $id): Response
    {
        $post = $doctrine->getRepository(Post::class)->find($id);

        if (!$post) {
            throw $this->createNotFoundException(
                'No post found for id '.$id
            );
        }

        return $this->render('post/index.html.twig', [
            'title' => $post->getTitle(),
        ]);
    }
}
