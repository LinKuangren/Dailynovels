<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Comment;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;

class CommentController extends AbstractController
{
    #[Route('/admin/comment/{id}/delete', name: 'delete_comment')]
    public function delete(Comment $comment, PersistenceManagerRegistry $doctrine): RedirectResponse
    {
        $novelId = $comment->getNovels()->getTitle();

        $entityManager = $doctrine->getManager();
        $entityManager->remove($comment);
        $entityManager->flush();

        return $this->redirectToRoute('show', ['title' => $novelId,]);
    }

    #[Route('/admin/comment/{id}/deletes', name: 'delete_comment_admin')]
    public function deletes(Comment $comment, PersistenceManagerRegistry $doctrine): RedirectResponse
    {
        $novelId = $comment->getNovels()->getTitle();

        $entityManager = $doctrine->getManager();
        $entityManager->remove($comment);
        $entityManager->flush();

        return $this->redirectToRoute('all_comment');
    }
}
