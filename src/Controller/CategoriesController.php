<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Categories;
use App\Repository\CategoriesRepository;
use App\Form\CategoriesType;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CategoriesController extends AbstractController
{
    #[Route('/categories', name: 'categories')]
    public function index(CategoriesRepository $categoriesRepo, PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $categoriesRepo->paginationCategorie(),
            $request->query->get('page', 1),
            10
        );

        return $this->render('categories/index.html.twig', [
            'categories' => $pagination,
            'controller_name' => 'Toutes les categories'
        ]);
    }

    #[Route('/categories/{name}', name: 'categories_show')]
    public function show(CategoriesRepository $categoriesRepo, PaginatorInterface $paginator, Request $request, string $name): Response
    {
        $categories = $categoriesRepo->findOneBy(["name" => $name]);
        $all_categories = $categoriesRepo->findAll();
        $novels = $categories->getNovels();

        $pagination = $paginator->paginate(
            $novels,
            $request->query->get('page', 1),
            10
        );

        return $this->render('categories/show.html.twig', [
            'categories' => $categories,
            'all_categories' => $all_categories,
            'novels' => $pagination,
        ]);
    }

    #[Route('/admin/categories/ajout', name: 'categories_ajout')]
    #[IsGranted('ROLE_ADMIN')]
    public function add(Request $request, PersistenceManagerRegistry $doctrine): Response
    {
        $categorie = new Categories;

        $form = $this->createForm(CategoriesType::class, $categorie);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $doctrine->getManager();
            $em->persist($categorie);
            $em->flush();

            $this->addFlash(
                'success',
                'La catégorie est créé.'
            );

            return $this->redirectToRoute('categories');
        }
        
        return $this->render('categories/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/categories/{id}/modifier', name: 'categories_modifier')]
    #[IsGranted('ROLE_ADMIN')]
    public function put(Request $request, Categories $categorie, PersistenceManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(CategoriesType::class, $categorie);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->flush();

            $this->addFlash(
                'success',
                'La categorie est modifié.'
            );

            return $this->redirectToRoute('categories');
        }

        return $this->render('categories/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/categories/{id}', name: 'categories_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Categories $categorie, PersistenceManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($categorie);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'La categorie est supprimé.'
        );

        return $this->redirectToRoute('categories');
    }
}