<?php

namespace App\Controller;

use App\Entity\Newsletter;
use App\Form\NewsletterType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

#[Route('/')]
class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);

    }
    #[Route('/back/dashboard/newsletter', name: 'app_dashboard_newsletter', methods: ['GET', 'POST'])]
    public function newsletter(UserRepository $userRepository,Request $request, MailerInterface $mailer): Response
    {
        $newsletter = new Newsletter();
        $form = $this->createForm(NewsletterType::class, $newsletter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $users = $userRepository->findEmails();
            
            $email = (new TemplatedEmail())
            ->from(new Address('w311940@gmail.com', 'Softtodo'))
            ->to(...$users)
            ->subject($newsletter->getSubject())
            ->html($newsletter->getBody());
            
            try {
                $mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                
            }
            return $this->redirectToRoute('app_dashboard_newsletter', ['success' => true], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('dashboard/newsletter.html.twig', [
            'newsletter' => $newsletter,
            'form' => $form,
            'success' => isset($_GET['success'])
        ]);
    }
}
