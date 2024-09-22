<?php

namespace App\Controller;

use SensioLabs\AnsiConverter\AnsiToHtmlConverter;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ScrapeController extends AbstractController
{
    private KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    #[Route('/scrape', name:'app_scrape')]
    #[IsGranted('ROLE_ADMIN')]
    public function scrape(Request $request): Response
    {
        return $this->render('scrape/index.html.twig');
    }

    #[Route('/scrape/run', name:'app_scrape_run', methods:['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function runScraping(): Response
    {
        // Créer une instance de l'application console
        $application = new Application($this->kernel);
        $application->setAutoExit(false); // Pour éviter l'arrêt de l'application après l'exécution de la commande

        $input = new ArrayInput([
            'command' => 'app:scrape',
        ]);

        // Utiliser BufferedOutput pour capturer la sortie de la commande
        $output = new BufferedOutput(OutputInterface::VERBOSITY_NORMAL, true); // true pour la sortie colorée

        $application->run($input, $output);

        // Récupérer la sortie de la commande
        $content = $output->fetch();

        // Convertir la sortie colorée ANSI en HTML
        $converter = new AnsiToHtmlConverter();
        $htmlContent = $converter->convert($content);

        // Retourner la sortie en HTML
        return new Response($htmlContent);
    }
}
