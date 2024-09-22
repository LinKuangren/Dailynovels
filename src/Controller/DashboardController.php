<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/admin')]
class DashboardController extends AbstractController
{
    #[Route('/dashboard/comment', name: 'all_comment')]
    public function comment(CommentRepository $commentRepo, PaginatorInterface $paginator, Request $request): Response
    {
        $comment = $commentRepo->findAll();

        $pagination = $paginator->paginate(
            $comment,
            $request->query->get('page', 1),
            10
        );

        return $this->render('dashboard/comment_dashboard.html.twig', [
            'comments' => $pagination,
            'controller_name' => 'Toutes les commentaires'
        ]);
    }

    #[Route('/dashboard/user', name: 'all_user')]
    public function user(UserRepository $userRepo, PaginatorInterface $paginator, Request $request): Response
    {
        $user = $userRepo->findAll();

        $pagination = $paginator->paginate(
            $user,
            $request->query->get('page', 1),
            10
        );

        return $this->render('dashboard/user_dashboard.html.twig', [
            'users' => $pagination,
            'controller_name' => 'Toutes les utilisateurs'
        ]);
    }
}
