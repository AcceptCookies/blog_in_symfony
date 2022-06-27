<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response ;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    #[Route('/comment/create', name: 'create_comment', methods: 'GET|POST')]
    public function create(Request $request, ManagerRegistry $doctrine): Response
    {
        $comment = new Comment();
        $entityManager = $doctrine->getManager();
        $authors = $doctrine->getRepository(Author::class)->findAll();
        $fakeAuthor = $authors[0];
        $posts = $doctrine->getRepository(Post::class)->findAll();
        $fakePost = $posts[0];
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newComment = $form->getData();
            $newComment->setCreated();
            $newComment->setAuthor($fakeAuthor);
            $newComment->setPost($fakePost);
            $newComment = $form->getData();
            $entityManager->persist($newComment);
            $entityManager->flush();

            return $this->redirectToRoute('show_post', ['id' => $fakePost->getId()]);
        }

        return $this->render('comments/create.html.twig',[
            'form' => $form->createView()
        ]);
    }
}
