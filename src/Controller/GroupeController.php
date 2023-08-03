<?php

namespace App\Controller;

use App\Entity\Groupe;
use App\Form\GroupeType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/groupe')]
class GroupeController extends AbstractController
{
    #[Route('/', name: 'app_groupe_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $groupes = $entityManager
            ->getRepository(Groupe::class)
            ->findAll();

        return $this->render('groupe/index.html.twig', [
            'groupes' => $groupes,
        ]);
    }

    #[Route('/new', name: 'app_groupe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $groupe = new Groupe();
        $form = $this->createForm(GroupeType::class, $groupe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $groupe->setCreatedAt(new DateTime());
            $groupe->setUpdatedAt(new DateTime());
            $groupe->setEnabled(1);
            $entityManager->persist($groupe);
            $entityManager->flush();

            return $this->redirectToRoute('app_groupe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('groupe/new.html.twig', [
            'groupe' => $groupe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_groupe_show', methods: ['GET'])]
    public function show(Groupe $groupe): Response
    {
        return $this->render('groupe/show.html.twig', [
            'groupe' => $groupe,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_groupe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Groupe $groupe, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GroupeType::class, $groupe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $groupe->setUpdatedAt(new DateTime());
            $entityManager->flush();

            return $this->redirectToRoute('app_groupe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('groupe/edit.html.twig', [
            'groupe' => $groupe,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_groupe_delete', methods: ['POST','GET'])]
    public function delete(Request $request, Groupe $groupe, EntityManagerInterface $entityManager): Response
    {
        $groupe->setEnabled(0);
            $entityManager->persist($groupe);
           
            $entityManager->flush();
        

            return $this->redirectToRoute("app_groupe_index");

    }
    #[Route('/activer/{id}', name: 'app_groupe_activer', methods: ['POST','GET'])]
    public function activer(Request $request, Groupe $groupe, EntityManagerInterface $entityManager): Response
    {
       
            $groupe->setEnabled(1);
            $entityManager->persist($groupe);
            $entityManager->flush();
        

            return $this->redirectToRoute("app_groupe_index");

    }
}
