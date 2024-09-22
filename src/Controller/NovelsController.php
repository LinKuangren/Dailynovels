<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Novels;
use App\Entity\Rating;
use App\Entity\Comment;
use App\Repository\NovelsRepository;
use App\Repository\RatingRepository;
use App\Form\NovelsType;
use App\Form\CommentType;
use App\Form\RatingType;
use App\Form\RechercheAvanceType;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class NovelsController extends AbstractController
{
    #[Route('/novels', name: 'novels')]
    public function index(NovelsRepository $novelsRepo, PaginatorInterface $paginator, Request $request): Response
    {
        $user = $this->getUser();

        $activeNovels = $novelsRepo->findBy(['Visibilitie' => true], ['title' => 'ASC']);

        // Permet au administrateur de voir les novels non visible.
        if ($this->isGranted('ROLE_ADMIN')) {
            $allNovels = $novelsRepo->findBy([], ['title' => 'ASC']);
        } else {
            $allNovels = $activeNovels;
        }

        $pagination = $paginator->paginate(
            $allNovels,
            $request->query->get('page', 1),
            12 //nombre qui s'affiche par pagination
        );

        return $this->render('novels/index.html.twig', [
            'user' => $user,
            'novels' => $pagination,
        ]);
    }

    #[Route('/novels/{letter}', name: 'novels_index_alphabetical')]
    public function alphabetical(NovelsRepository $novelsRepo, PaginatorInterface $paginator, Request $request, $letter): Response
    {
        // Récupérez les romans commençant par la lettre sélectionnée
        $novels = $novelsRepo->findByStartingLetter($letter);

        $pagination = $paginator->paginate(
            $novels,
            $request->query->get('page', 1),
            12
        );

        return $this->render('novels/index.html.twig', [
            'novels' => $pagination,
        ]);
    }

    #[Route('/admin/novels/ajout', name: 'novels_ajout')]
    #[IsGranted('ROLE_ADMIN')]
    public function add(Request $request, PersistenceManagerRegistry $doctrine): Response
    {
        $novel = new Novels;
        $user = $this->getUser();

        $form = $this->createForm(NovelsType::class, $novel);

        $form->handleRequest($request);

        $defaultImage = $novel->setDefaultImageIfEmpty();
        $defaultDescrip = 'Rien pour le moment.';

        if($form->isSubmitted() && $form->isValid()){
            $novel->setCreatedAt(new \DateTimeImmutable());
            $novel->setUpdatedAt(new \DateTimeImmutable());

            if (!$novel->getImageName()) {
                $novel->setImageName($defaultImage);
            }

            if (!$novel->getDescription()) {
                $novel->setDescription($defaultDescrip);
            }

            $em = $doctrine->getManager();
            $em->persist($novel);
            $em->flush();

            $this->addFlash(
                'success',
                'La novel est créé.'
            );

            return $this->redirectToRoute('novels');
        }
        
        return $this->render('novels/add.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/admin/novels/{title}/modifier', name: 'novels_modifier')]
    #[IsGranted('ROLE_ADMIN')]
    public function put(NovelsRepository $novelsRepo, Request $request, Novels $novel, PersistenceManagerRegistry $doctrine, string $title): Response
    {
        $user = $this->getUser();
        $novel = $novelsRepo->findOneBy(['title' => $title]);

        $form = $this->createForm(NovelsType::class, $novel);

        $form->handleRequest($request);

        $currentImageName = $novel->getImageName();

        if ($form->isSubmitted() && $form->isValid()) {
            // Si une l'image est defaut.svg de base
            if ($novel->getImageFile() !== null) {
                if ($currentImageName === 'defaut.svg') {
                    $novel->setImageName(null);
                }
            }

            $novel->setUpdatedAt(new \DateTimeImmutable());

            $entityManager = $doctrine->getManager();
            $entityManager->persist($novel);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'La novel est modifié.'
            );

            return $this->redirectToRoute('novels');
        }

        return $this->render('novels/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'novel' => $novel,
        ]);
    }

    #[Route('/novels/{title}/detail', name: 'show', methods: ['GET', 'POST'])]
    public function show(NovelsRepository $novelsRepo, Novels $novels, PaginatorInterface $paginator, RatingRepository $ratingRepo, Request $request, EntityManagerInterface $entityManager, PersistenceManagerRegistry $doctrine, string $title): Response
    {
        $novel = $novelsRepo->findOneBy(['title' => $title]);

        if (!$novel) {
            throw $this->createNotFoundException('Le roman demandé n\'a pas été trouvé.');
        }
        
        $sortOrder = $request->query->get('sortOrder', 'DESC');

        $chapitres = $novel->getChapitres();

        if ($sortOrder === 'DESC') {
            $chapitres->initialize();
            $chapitres = $chapitres->toArray();
            usort($chapitres, function($a, $b) {
                return $b->getCreatedAt() <=> $a->getCreatedAt();
            });
        } elseif ($sortOrder === 'ASC') {
            $chapitres->initialize();
            $chapitres = $chapitres->toArray();
            usort($chapitres, function($a, $b) {
                return $a->getCreatedAt() <=> $b->getCreatedAt();
            });
        }

        $rating = new Rating();
        $form = $this->createForm(RatingType::class, $rating);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $rating->setUser($this->getUser())
                ->setNovels($novel);

            $existingRating = $ratingRepo->findOneBy([
                'user' => $this->getUser(),
                'novels' => $novels
            ]);

            if (!$existingRating) {
                $entityManager->persist($rating);
            } else {
                $existingRating->setRating(
                    $form->getData()->getRating()
                );
            }

            $entityManager->flush();

            $this->addFlash(
                'success',
                'Votre note a bien été prise en compte.'
            );

            return $this->redirectToRoute('show', ['title' => $novel->getTitle()]);
        }

        $pagination = $paginator->paginate(
            $chapitres,
            $request->query->getInt('page', 1),
            20
        );

        $comments = $novel->getComments();
        $newComment = new Comment();

        $commentForm = $this->createForm(CommentType::class, $newComment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            // Définir la novel et l'utilisateur associés pour le nouveau commentaire.
            $newComment->setNovels($novel);
            $newComment->setUser($this->getUser());

            $this->getUser()->addComment($newComment);

            $em = $doctrine->getManager();
            $em->persist($newComment);
            $em->flush();

            return $this->redirectToRoute('show', ['title' => $title,]);
        }

        return $this->render('novels/show.html.twig', [
            'novel' => $novel,
            'form' => $form->createView(),
            'pagination' => $pagination,
            'comments' => $comments, 
            'commentForm' => $commentForm->createView(),]);
    }

    #[Route('/admin/novels/{id}', name: 'novels_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Novels $novels, PersistenceManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $chapitres = $novels->getChapitres();
        foreach ($chapitres as $chapitre) {
            $novels->removeChapitre($chapitre);
            $entityManager->remove($chapitre);
        }
        $comments = $novels->getComments();
        foreach ($comments as $comment) {
            $novels->removeComment($comment);
            $entityManager->remove($comment);
        }

        // Si l'image de la novel est 'defaut.svg', ne pas la supprimer
        if ($novels->getImageName() === 'defaut.svg') {
            // Supprimer la référence de l'image
            $novels->setImageName(null);
        }
        $entityManager->remove($novels);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'La novel est supprimé.'
        );

        return $this->redirectToRoute('novels');
    }

    #[Route('/novels/{id}/favoris', name: 'add_favoris')]
    public function favorite(Novels $novel, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user) {
            // Redirigez vers la page de connexion si l'utilisateur n'est pas connecté
            return $this->redirectToRoute('app_login');
        }

        if ($user->getFavoris()->contains($novel)) {
            // Retirez le roman des favoris de l'utilisateur
            $user->removeFavori($novel);
        } else {
            // Ajoutez le roman aux favoris de l'utilisateur
            $user->addFavori($novel);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('show', ['title' => $novel->getTitle()]);
    }

    #[Route('/novels/{id}/favoris-users', name: 'remove_favoris_user')]
    public function notfavorite(Novels $novel, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user) {
            // Redirigez vers la page de connexion si l'utilisateur n'est pas connecté
            return $this->redirectToRoute('app_login');
        }

        if ($user->getFavoris()->contains($novel)) {
            // Retirez le roman des favoris de l'utilisateur
            $user->removeFavori($novel);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('favoris', ['pseudo' => $user->getPseudo()]);
    }
    
    #[Route('/recherche', name: 'search')]
    public function search(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator)
    {
        $form = $this->createForm(RechercheAvanceType::class);
        $form->handleRequest($request);

        $results = [];


        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérez les données du formulaire
            $formData = $form->getData();

            // Utilise Doctrine QueryBuilder pour construire la requête
            $queryBuilder = $entityManager->getRepository(Novels::class)->createQueryBuilder('n');

            // Ajoutez des clauses WHERE pour les catégories
            if (!$formData['Categories']->isEmpty()) {
                $queryBuilder
                    ->join('n.Categories', 'c')
                    ->andWhere('c.id IN (:categoryIds)')
                    ->setParameter('categoryIds', $formData['Categories']);
            }

            // Ajoutez des clauses WHERE pour les tags
            if (!$formData['Tags']->isEmpty()) {
                $queryBuilder
                    ->join('n.Tags', 't')
                    ->andWhere('t.id IN (:tagIds)')
                    ->setParameter('tagIds', $formData['Tags']);
            }

            if (!$formData['TagsExcluded']->isEmpty()) {
                $queryBuilder
                    ->leftJoin('n.Tags', 't')
                    ->andWhere('t.id NOT IN (:tagIdsExcluded)')
                    ->setParameter('tagIdsExcluded', $formData['TagsExcluded']);
            }

            if (!empty($formData['statut'])) {
                $queryBuilder
                    ->andWhere('n.statut = :statut')
                    ->setParameter('statut', $formData['statut']);
            }

            // Ajoutez des clauses ORDER BY en fonction du critère de tri sélectionné
            if ($formData['orderBy'] === 'favorites') {
                $queryBuilder
                    ->leftJoin('n.favoris', 'f')
                    ->groupBy('n.id')
                    ->orderBy('COUNT(f.id)', 'DESC');
            } elseif ($formData['orderBy'] === 'chapitres') {
                $queryBuilder
                    ->leftJoin('n.chapitres', 'ch')
                    ->groupBy('n.id')
                    ->orderBy('COUNT(ch.id)', 'DESC');
            } elseif ($formData['orderBy'] === 'commentaires') {
                $queryBuilder
                    ->leftJoin('n.comments', 'co')
                    ->groupBy('n.id')
                    ->orderBy('COUNT(co.id)', 'DESC');
            }

            // Exécutez la requête
            $results = $queryBuilder->getQuery()->getResult();
        }

        $pagination = $paginator->paginate(
            $results, // Les résultats à paginer
            $request->query->getInt('page', 1),
            100
        );

        return $this->render('main/recherche.html.twig', [
            'form' => $form->createView(),
            'pagination' => $pagination,
        ]);
    }
}