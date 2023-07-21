<?php

namespace App\Controller;

use App\Entity\Session;
use App\Form\SessionType;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/session')]
class SessionController extends AbstractController
{
    #[Route('/', name: 'app_session_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $sessions = $entityManager
            ->getRepository(Session::class)
            ->findAll();

        return $this->render('session/index.html.twig', [
            'sessions' => $sessions,
        ]);
    }

    #[Route('/new', name: 'app_session_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $session = new Session();
        $form = $this->createForm(SessionType::class, $session);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date=new \DateTime();
            $session->setCreatedAt($date);
            $session->setUpdatedAt($date);
            $entityManager->persist($session);
            $entityManager->flush();

            return $this->redirectToRoute('app_session_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('session/new.html.twig', [
            'session' => $session,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_session_show', methods: ['GET'])]
    public function show(Session $session): Response
    {
        return $this->render('session/show.html.twig', [
            'session' => $session,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_session_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Session $session, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SessionType::class, $session);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date=new \DateTime();
            $session->setUpdatedAt($date);
            $entityManager->flush();

            return $this->redirectToRoute('app_session_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('session/edit.html.twig', [
            'session' => $session,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_session_delete', methods: ['POST'])]
    public function delete(Request $request, Session $session, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$session->getId(), $request->request->get('_token'))) {
            $entityManager->remove($session);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_session_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/activer/{id}', name: 'app_session_activer')]
    public function activer(SessionRepository $repository,$id,ManagerRegistry $doctrine){
 $session=$repository->find($id);
 $session->setEnabled(true);
 $em=$doctrine->getManager();
 $em->flush();
 return $this->redirectToRoute("app_session_index");

    }
    #[Route('/desactiver/{id}', name: 'app_session_desactiver')]
    public function desactiver(SessionRepository $repository,$id,ManagerRegistry $doctrine){
        $session=$repository->find($id);
        $session->setEnabled(false);
        $em=$doctrine->getManager();
        $em->flush();
        return $this->redirectToRoute("app_session_index");

    }
}
