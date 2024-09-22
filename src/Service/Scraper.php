<?php

namespace App\Service;

use Symfony\Component\Panther\Client;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Novels;
use App\Entity\Chapitres;

class Scraper
{
    private $client;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->client = Client::createChromeClient();
        $this->entityManager = $entityManager;
    }

    public function scrape()
    {
        $this->client->get('https://xiaowaz.fr/page/25/');

        // Cliquer sur le lien de la deuxième page au début
        $this->clickSecondPage();

        $quote_no = 0;
        while (true) {
            $crawler = $this->client->waitFor('.card_body');

            $crawler->filter('.card_body')->each(function ($crawler) use (&$quote_no) {
                $quote_no++;
                $title = $crawler->filter('.card_title')->text();
                $novelTitle = $crawler->filter('.cat-links')->text();
                $chapterUrl = $crawler->filter('.card_body .btn-primary')->attr('href');
                echo $quote_no . ") " . $title . " - " . $novelTitle . " - " . $chapterUrl . PHP_EOL;

                // Vérification et traitement de la novel et du chapitre
                $this->processNovelAndChapter($novelTitle, $title, $chapterUrl);
            });

            try {
                $this->client->waitFor('.card_body');
                $this->client->clickLink('Précédent');
            } catch (\Exception $e) {
                // Si le bouton "Prècédent" n'existe plus, sortir de la boucle
                break;
            }
        }
        // Fermer le client si c'est terminé
        $this->client->close();
    }

    private function clickSecondPage()
    {
        try {
            $this->client->get('https://xiaowaz.fr/page/25/');
            $secondPageLink = $this->client->waitFor('.nav-links')->filter('a.page-numbers')->eq(0)->link();
            $this->client->click($secondPageLink);
            $this->client->waitFor('.card_body');
        } catch (\Exception $e) {
            echo "Erreur lors du clic sur la deuxième page : " . $e->getMessage();
        }
    }

    private function processNovelAndChapter($novelTitle, $chapterTitle, $chapterUrl)
    {
        $novel = $this->checkNovelExists($novelTitle);

        if ($novel) {
            if ($this->checkChapterExists($novel, $chapterTitle)) {
                echo "Le chapitre \"$chapterTitle\" existe déjà dans la base de données pour le roman \"$novelTitle\". Passage au suivant." . PHP_EOL;
                return;
            }
            $this->addChapterToExistingNovel($novel, $chapterTitle, $chapterUrl);
        } else {
            $this->createNovelAndAddChapter($novelTitle, $chapterTitle, $chapterUrl);
        }
    }

    private function checkNovelExists($novelTitle)
    {
        return $this->entityManager->getRepository(Novels::class)->findOneBy(['title' => $novelTitle]);
    }

    private function checkChapterExists($novel, $chapterTitle)
    {
        return $this->entityManager->getRepository(Chapitres::class)->findOneBy([
            'name' => $chapterTitle,
            'novels' => $novel
        ]) !== null;
    }

    private function addChapterToExistingNovel($novel, $chapterTitle, $chapterUrl)
    {
        $chapter = new Chapitres();
        $chapter->setName($chapterTitle);
        $chapter->setLink($chapterUrl);
        $chapter->setNovels($novel);
        $chapter->setCreatedAt(new \DateTimeImmutable());
        $chapter->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($chapter);
        $this->entityManager->flush();
    }

    private function createNovelAndAddChapter($novelTitle, $chapterTitle, $chapterUrl)
    {
        $defaultImage = 'defaut.svg';
        $defaultDescrip = 'Rien pour le moment.';

        $novel = new Novels();
        $novel->setTitle($novelTitle);
        $novel->setDescription($defaultDescrip);
        $novel->setImageName($defaultImage);
        $novel->setVisibilitie(true);
        $novel->setStatut('en cours');
        $novel->setCreatedAt(new \DateTimeImmutable());
        $novel->setUpdatedAt(new \DateTimeImmutable());

        $chapter = new Chapitres();
        $chapter->setName($chapterTitle);
        $chapter->setLink($chapterUrl);
        $chapter->setNovels($novel);
        $chapter->setCreatedAt(new \DateTimeImmutable());
        $chapter->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($novel);
        $this->entityManager->persist($chapter);
        $this->entityManager->flush();
    }
}
