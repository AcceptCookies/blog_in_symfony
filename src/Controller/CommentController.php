<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentFormType;
use App\Services\CommentService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response ;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    #[Route('/comment/create', name: 'create_comment', methods: 'GET|POST')]
    public function create(Request $request, ManagerRegistry $doctrine, CommentService $commentService): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);

        return $this->render('comments/create.html.twig',[
            'form' => $commentService->createCommentForm($request, $doctrine, $form)->createView()
        ]);
    }
}
