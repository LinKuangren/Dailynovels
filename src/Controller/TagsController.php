<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Tags;
use App\Repository\TagsRepository;
use App\Form\TagsType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

class TagsController extends AbstractController
{

    #[Route('/tags', name: 'tags')]
    public function index(TagsRepository $tagsRepo, PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $tagsRepo->paginationTag(),
            $request->query->get('page', 1),
            12
        );

        return $this->render('tags/index.html.twig', [
            'tags' => $pagination,
            'controller_name' => 'Tous les tags',
        ]);
    }

    #[Route('/tags/{name}/detail', name: 'tags_show')]
    public function show(TagsRepository $tagsRepo, Tags $tag, PaginatorInterface $paginator, Request $request, string $name): Response
    {
        $tags = $tagsRepo->findOneBy(['name' => $name]);
        
        $novels = $tag->getNovels();

        $pagination = $paginator->paginate(
            $novels,
            $request->query->get('page', 1),
            12
        );

        return $this->render('tags/show.html.twig', [
            'tags' => $tags,
            'novels' => $pagination,
        ]);
    }

    #[Route('/tags/{letter}', name: 'tags_index_alphabetical')]
    public function alphabetical(TagsRepository $tagsRepo, PaginatorInterface $paginator, Request $request, $letter): Response
    {
        // Récupérez les romans commençant par la lettre sélectionnée
        $tags = $tagsRepo->findByStartingLetter($letter);

        $pagination = $paginator->paginate(
            $tags,
            $request->query->get('page', 1),
            12
        );

        return $this->render('tags/index.html.twig', [
            'tags' => $pagination,
            'controller_name' => 'Tous les tags',
        ]);
    }

    #[Route('/admin/tags/ajout', name: 'tags_ajout')]
    #[IsGranted('ROLE_ADMIN')]
    public function add(Request $request, PersistenceManagerRegistry $doctrine): Response
    {
        $tag = new Tags;

        $form = $this->createForm(TagsType::class, $tag);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $doctrine->getManager();
            $em->persist($tag);
            $em->flush();

            $this->addFlash(
                'success_categorie',
                'Le tag est créé.'
            );

            return $this->redirectToRoute('tags');
        }
        
        return $this->render('tags/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/tags/{id}/modifier', name: 'tags_modifier')]
    #[IsGranted('ROLE_ADMIN')]
    public function put(Request $request, Tags $tag, PersistenceManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(tagsType::class, $tag);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Le tag est modifié.'
            );

            return $this->redirectToRoute('tags');
        }

        return $this->render('tags/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/tags/{id}', name: 'tags_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Tags $tags, PersistenceManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($tags);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Le tag est supprimé.'
        );

        return $this->redirectToRoute('tags');
    }
}