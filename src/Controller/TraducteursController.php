<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Traducteurs;
use App\Repository\TraducteursRepository;
use App\Form\TraducteursType;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TraducteursController extends AbstractController
{
    #[Route('/traducteurs', name: 'traducteurs')]
    public function index(TraducteursRepository $traducteursRepo, PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $traducteursRepo->paginationTraducteur(),
            $request->query->get('page', 1),
            10
        );

        return $this->render('traducteurs/index.html.twig', [
            'traducteurs' => $pagination,
            'controller_name' => 'Traducteurs'
        ]);
    }

    #[Route('/admin/traducteurs/ajout', name: 'traducteurs_ajout')]
    #[IsGranted('ROLE_ADMIN')]
    public function add(Request $request, PersistenceManagerRegistry $doctrine): Response
    {
        $traducteur = new Traducteurs;

        $form = $this->createForm(TraducteursType::class, $traducteur);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $doctrine->getManager();
            $em->persist($traducteur);
            $em->flush();

            $this->addFlash(
                'success',
                'Le traducteur est créé.'
            );

            return $this->redirectToRoute('traducteurs');
        }
        
        return $this->render('traducteurs/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/traducteurs/{id}/modifier', name: 'traducteurs_modifier')]
    #[IsGranted('ROLE_ADMIN')]
    public function put(Request $request, Traducteurs $traducteur, PersistenceManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(TraducteursType::class, $traducteur);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Le traducteur est modifié.'
            );

            return $this->redirectToRoute('traducteurs');
        }

        return $this->render('traducteurs/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/traducteurs/{id}/detail', name: 'traducteurs_show')]
    public function show(TraducteursRepository $traducteursRepo, int $id): Response
    {
        $traducteur = $traducteursRepo->find($id);

        return $this->render('traducteurs/show.html.twig', ['traducteur' => $traducteur]);
    }

    #[Route('/admin/traducteurs/{id}', name: 'traducteurs_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Traducteurs $traducteur, PersistenceManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($traducteur);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Le traducteur est supprimé.'
        );

        return $this->redirectToRoute('traducteurs');
    }
}