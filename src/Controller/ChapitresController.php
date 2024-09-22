<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use App\Entity\Chapitres;
use App\Form\ChapitresType;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ChapitresController extends AbstractController
{
    #[Route('/admin/chapitres/ajout', name: 'chapitres_ajout')]
    #[IsGranted('ROLE_ADMIN')]
    public function add(Request $request, PersistenceManagerRegistry $doctrine): Response
    {
        $chapitre = new Chapitres;

        $form = $this->createForm(ChapitresType::class, $chapitre);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $doctrine->getManager();
            $em->persist($chapitre);
            $em->flush();

            $this->addFlash(
                'success',
                'Le chapitre est créé.'
            );

            return $this->redirectToRoute('novels');
        }
        
        return $this->render('chapitres/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/chapitres/{id}/modifier', name: 'chapitres_modifier')]
    #[IsGranted('ROLE_ADMIN')]
    public function put(Request $request, Chapitres $chapitre, PersistenceManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(ChapitresType::class, $chapitre);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Le chapitre est modifié.'
            );

            return $this->redirectToRoute('novels');
        }

        return $this->render('chapitres/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/chapitres/{id}/delete', name: 'chapitres_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Chapitres $chapitre, PersistenceManagerRegistry $doctrine): Response
    {
        $novelId = $chapitre->getNovels()->gettitle();

        $entityManager = $doctrine->getManager();
        $entityManager->remove($chapitre);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Le chapitre est supprimé.'
        );

        return $this->redirectToRoute('show', ['title' => $novelId]);
    }
}
