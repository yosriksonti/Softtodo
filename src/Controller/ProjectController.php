<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\User;
use App\Form\ProjectType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;


class ProjectController extends AbstractController
{   
    #[Route('/project', name: 'app_project_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, ProjectRepository $projectRepository): Response
    {
        
        $projects = $entityManager
                ->getRepository(Project::class)
                ->findAll();
        $today = new \DateTime();
        $title =  isset($_GET['title']) ? $_GET['title'] : "";
        $status =  isset($_GET['status']) ? $_GET['status'] : "";
        $filename =  isset($_GET['filename']) ? $_GET['filename'] : "";
        $projects = $projectRepository->findByClause($title,$status,$filename);
        return $this->render('project/index.html.twig', [
            'projects' => $projects,
            'today' => $today,
            'title'=> $title,
            'status'=> $status,
            'filename'=> $filename,
        ]);
    }

    #[Route('/back/project/new', name: 'app_project_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ProjectRepository $projectRepository): Response
    {
        
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);
        
        

        if ($form->isSubmitted() && $form->isValid()) {
            $project->setCreatedAt(new \DateTime());
            $project->setUpdatedAt(new \DateTime());
            $projectRepository->add($project, true);
            return $this->redirectToRoute('app_project_show', ['id' => $project->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('project/new.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    #[Route('/project/{id}', name: 'app_project_show', methods: ['GET'])]
    public function show(Project $project, EntityManagerInterface $entityManager): Response
    {
        $today = new \DateTime();
        return $this->render('project/show.html.twig', [
            'project' => $project,
            'today' => $today
        ]);
    }

    #[Route('/back/project/{id}/edit', name: 'app_project_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Project $project, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            if($project->getStatus() != $form->get('status')->getData()) {
                $project->setStatus($form->get('status')->getData());
                $admins = $entityManager
                ->getRepository(User::class)
                ->findBy(['isAdmin' => true]);
                $receivers = [];
                foreach($admins as $admin) {
                    $receivers[] = $admin->getEmail();
                }
                $email = (new TemplatedEmail())
                ->from(new Address('w311940@gmail.com', 'Softtodo'))
                ->to(...$receivers)
                ->subject("Project - ".$project->getTitle()." has been modified")
                ->html("Project - ".$project->getTitle()." status has been changed to ".$project->getStatus());
                try {
                    $mailer->send($email);
                } catch (TransportExceptionInterface $e) {
                    
                }
            }
            $project->setUpdatedAt(new \DateTime());
            $entityManager->flush();
            
        return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('project/edit.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    #[Route('/back/project/{id}', name: 'app_project_delete', methods: ['POST'])]
    public function delete(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        
        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->request->get('_token'))) {
            $entityManager->remove($project);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
    }
}
