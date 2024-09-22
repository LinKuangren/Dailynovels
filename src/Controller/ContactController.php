<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use App\Entity\Contact;
use App\Form\ContactType;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function index(Request $request, MailerInterface $mailer, EntityManagerInterface $entityManager): Response
    {
        $contact = new Contact;

        if ($this->getUser()) {
            $contact->setEmail($this->getUser()->getEmail());
        }

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if($form->IsSubmitted() && $form->IsValid()) {
            $contact = $form->getData();
            $entityManager->persist($contact);
            $entityManager->flush();

            // Email
            $email = (new TemplatedEmail())
                ->from($contact->getEmail())
                ->to('dailynovels@gmail.com')
                ->subject($contact->getSubject())
                // path of the Twig template to render
                ->htmlTemplate('emails/contact.html.twig')

                // pass variables (name => value) to the template
                ->context([
                    'contact' => $contact,
                ]);

            $mailer->send($email);

            $this->addFlash(
                'success',
                'Votre demande à été envoyé avec succès !'
            );

            return $this->redirectToRoute('contact');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
