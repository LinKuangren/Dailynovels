<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\NovelsRepository;
use App\Repository\AnnoncesRepository;
use App\Repository\ChapitresRepository;
use Knp\Component\Pager\PaginatorInterface;

class MainController extends AbstractController
{
    #[Route('/', name: 'accueil')]
    public function index(NovelsRepository $novelsRepo, ChapitresRepository $chapitresRepo, AnnoncesRepository $annoncesRepo): Response
    {
        $latestAnnonces = $annoncesRepo->findBy([], ['CreatedAt' => 'desc'], 3);

        $latestNovels = $novelsRepo->findBy(['Visibilitie' => true], ['CreatedAt' => 'desc'], 12);

        // Récupérer les 100(2000) derniers chapitres
        $latestChapitres = $chapitresRepo->findBy([], ['CreatedAt' => 'DESC'], 2000);

        $filteredChapitres = [];
        $novelIds = [];

        // Filtrer pour garder seulement le dernier chapitre de chaque roman
        foreach ($latestChapitres as $chapitre) {
            $novelId = $chapitre->getNovels()->getId();

            // Vérifier si le roman est déjà traité
            if (!in_array($novelId, $novelIds)) {
                // Ajouter le chapitre au tableau filtré
                $filteredChapitres[] = $chapitre; 
                // Ajouter l'ID du roman pour ne plus l'inclure dans la suite
                $novelIds[] = $novelId;
            }

            // Si on a déjà 9 romans, on arrête
            if (count($filteredChapitres) === 9) {
                break;
            }
        }

        return $this->render('main/home.html.twig', [
            'controller_name' => 'Accueil',
            'novels' => $latestNovels,
            'annonces' => $latestAnnonces,
            'chapitres' => $filteredChapitres
        ]);
    }

    #[Route('/presentation', name: 'presentation')]
    public function presentation(): Response
    {
        return $this->render('main/presentation.html.twig', [
            'controller_name' => 'Presentation',
        ]);
    }

    #[Route('/classement/{type}', name: 'classement')]
    public function classement(NovelsRepository $novelsRepo, PaginatorInterface $paginator, Request $request, $type = 'favoris'): Response
    {
        $novels = $novelsRepo->findBy(['Visibilitie' => true]);

        if ($type === 'favoris') {
            $favoris = $novelsRepo->findByFavorisClassement();
        } elseif ($type === 'chapitres') {
            $favoris = $novelsRepo->findByChapitresClassement();
        } elseif ($type === 'plus_recents') {
            $favoris = $novelsRepo->findByDateCreationClassement();
        }

        $pagination = $paginator->paginate(
            $favoris,
            $request->query->get('page', 1),
            10 //nombre qui s'affiche par pagination
        );

        return $this->render('main/classement.html.twig', [
            'novels' => $novels,
            'favoris' => $pagination,
            'type' => $type,
        ]);
    }

    #[Route('/rgpd', name: 'rgpd')]
    public function rgpd(): Response
    {
        return $this->render('main/rgpd.html.twig', [
            'controller_name' => 'RGPD',
        ]);
    }

    #[Route('/condition-d\'utilisation', name: 'conditions')]
    public function condition(): Response
    {
        return $this->render('main/condition.html.twig', [
            'controller_name' => 'Condition d\'utilisation',
        ]);
    }
}
