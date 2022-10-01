<?php

namespace App\Services;

use App\Entity\Author;
use App\Entity\Post;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

class CommentService
{
    public function createCommentForm(Request $request, ManagerRegistry $doctrine, $form)
    {
        $entityManager = $doctrine->getManager();
        $authors = $doctrine->getRepository(Author::class)->findAll();
        $fakeAuthor = $authors[0];
        $posts = $doctrine->getRepository(Post::class)->findAll();
        $fakePost = $posts[0];
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newComment = $form->getData();
            $newComment->setCreated();
            $newComment->setAuthor($fakeAuthor);
            $newComment->setPost($fakePost);
            $newComment = $form->getData();
            $entityManager->persist($newComment);
            $entityManager->flush();
        }

        return $form;
    }
}