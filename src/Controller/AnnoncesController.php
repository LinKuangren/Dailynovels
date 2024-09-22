<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Annonces;
use App\Repository\AnnoncesRepository;
use App\Form\AnnoncesType;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AnnoncesController extends AbstractController
{
    #[Route('/annonces', name: 'annonces')]
    public function index(AnnoncesRepository $annoncesRepo, PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $annoncesRepo->paginationAnnonce(),
            $request->query->get('page', 1),
            10
        );

        return $this->render('annonces/index.html.twig', [
            'annonces' => $pagination,
            'controller_name' => 'Toutes les annonces',
        ]);
    }

    #[Route('/admin/annonces/ajout', name: 'annonces_ajout')]
    #[IsGranted('ROLE_ADMIN')]
    public function add(Request $request, PersistenceManagerRegistry $doctrine): Response
    {

        $annonce = new Annonces;

        $form = $this-> createForm(AnnoncesType::class, $annonce);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $doctrine->getManager();
            $em->persist($annonce);
            $em->flush();

            $this->addFlash(
                'success',
                'L\'annonce est créé.'
            );

            return $this->redirectToRoute('annonces');
        }

        return $this->render('annonces/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/annonces/{id}/modifier', name: 'annonces_modifier')]
    #[IsGranted('ROLE_ADMIN')]
    public function put(Request $request, Annonces $annonce, PersistenceManagerRegistry $doctrine): Response
    {

        $form = $this->createForm(AnnoncesType::class, $annonce);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->flush();

            $this->addFlash(
                'success',
                'L\'annonce est modifié.'
            );

            return $this->redirectToRoute('annonces');
        }

        return $this->render('annonces/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/annonces/detail/{id}', name: 'show_annonce')]
    public function show(AnnoncesRepository $annoncesRepo, int $id): Response
    {
        $annonce = $annoncesRepo->find($id);

        if (!$annonce) {
            throw $this->createNotFoundException('L\'annonce demandé n\'a pas été trouvé.');
        }

        return $this->render('annonces/show.html.twig', ['annonce' => $annonce]);
    }

    #[Route('/admin/annonces/{id}', name: 'annonces_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Annonces $annonce, PersistenceManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($annonce);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'L\'annonce est supprimé.'
        );

        return $this->redirectToRoute('annonces');
    }
}
