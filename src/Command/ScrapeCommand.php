<?php

namespace App\Command;

use App\Service\Scraper;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:scrape',
    description: 'Exécuter le service de scrapping.'
)]
class ScrapeCommand extends Command
{
    public function __construct(
        private Scraper $scraper,
        private LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        // Le nom et la description de la commande sont définis avec l'attribut #[AsCommand]
    }

    private function executeScraper(SymfonyStyle $io): void
    {
        $io->title('Xiaowaz');
        $this->scraper->scrape();
    }

    // private function executeScraperAutre(SymfonyStyle $io): void
    // {
    //     $io->title('Autre');
    //     $this->scraperAutre->scrapeAutre();
    // }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Commencer le scrapping...');

        try {
            $this->executeScraper($io);
        } catch (\Exception $e) {
            $this->logger->error('Erreur durant le scrapping de Xiaowaz: ' . $e->getMessage());
            $io->error('Erreur durant le scrapping de Xiaowaz: ' . $e->getMessage());
        }

        // try {
        //     $this->executeScraperAutre($io);
        // } catch (\Exception $e) {
        //     $this->logger->error('Erreur durant le scrapping de Autre: ' . $e->getMessage());
        //     $io->error('Erreur durant le scrapping de Autre: ' . $e->getMessage());
        // }

        $io->success('Le scrapping est terminer avec succes.');

        return Command::SUCCESS;
    }
}
