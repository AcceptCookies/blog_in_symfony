<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Comment;
use App\Entity\Post;
use App\Form\PostFormType;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

const PAGE_NUMBER = 1;
const LIMIT_PER_PAGE = 4;

class PostController extends AbstractController
{
    private ObjectRepository $posts;
    private ObjectRepository $comments;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->posts = $doctrine->getRepository(Post::class);
        $this->comments = $doctrine->getRepository(Comment::class);
    }

    #[Route('/posts', name: 'posts', methods: 'GET')]
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $posts = $this->posts->findAll();
        $comments  = $this->comments->findAll();

        if (!$posts) {
            throw $this->createNotFoundException(
                'No posts found'
            );
        }

        $pagination = $paginator->paginate(
            $posts,
            $request->query->getInt('page', PAGE_NUMBER),
            LIMIT_PER_PAGE
        );

        return $this->render('posts/index.html.twig', [
            'comments' => $comments,
            'pagination' => $pagination
        ]);
    }

    #[Route('/posts/create', name: 'create_post', methods: 'GET|POST')]
    public function create(Request $request, ManagerRegistry $doctrine): Response
    {
        $post = new Post();
        $entityManager = $doctrine->getManager();

        $authors = $doctrine->getRepository(Author::class)->findAll();
        $fakeAuthor = $authors[0];

        $form = $this->createForm(PostFormType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPost = $form->getData();
            $newPost->setCreated();
            $newPost->setAuthor($fakeAuthor);

            $entityManager->persist($newPost);
            $entityManager->flush();

            return $this->redirectToRoute('posts');
        }

        return $this->render('posts/create.html.twig',[
            'form' => $form->createView()
        ]);
    }
    
    #[Route('/posts/{id}', name: 'show_post', methods: 'GET')]
    public function show(int $id): Response
    {
        $post = $this->posts->find($id);
        $comments  = $this->comments->findAll();

        if (!$post) {
            throw $this->createNotFoundException(
                'No posts found for id '.$id
            );
        }

        return $this->render('posts/show.html.twig', [
            'post' => $post,
            'comments' => $comments
        ]);
    }




}
