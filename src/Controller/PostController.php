<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostFormType;
use App\Services\PostService;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use \Knp\Component\Pager\Pagination\PaginationInterface as Pagination;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    private PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    #[Route('/posts', name: 'posts', methods: 'GET')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $pagination = $this->postService->getAllPosts($request, $paginator);

        if (!$pagination instanceof Pagination) {
            return $this->redirectToRoute('create_post');
        }

        return $this->render('posts/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    #[Route('/posts/create', name: 'create_post', methods: 'GET|POST')]
    public function create(Request $request, ManagerRegistry $doctrine): Response
    {
        $post = new Post();
        $form = $this->createForm(PostFormType::class, $post);

        return $this->render('posts/create.html.twig',[
            'form' => $this->postService->createPostForm($request, $doctrine, $form)->createView()
        ]);
    }
    
    #[Route('/posts/{id}', name: 'show_post', methods: 'GET')]
    public function show(int $id): Response
    {
        return $this->render('posts/show.html.twig', [
            'post' => $this->postService->getPost($id),
            'comments' => $this->postService->getPostComments()
        ]);
    }
}
