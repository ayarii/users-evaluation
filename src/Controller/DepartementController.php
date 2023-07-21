<?php

namespace App\Controller;

use App\Entity\Departement;
use App\Form\DepartementType;
use App\Repository\DepartementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

#[Route('/departement')]
class DepartementController extends AbstractController
{
    #[Route('/', name: 'app_departement_index', methods: ['GET'])]
    public function index(DepartementRepository $departementRepository): Response
    {
        $departements= $departementRepository->findAll();
        return $this->render('departement/index.html.twig', [
            'departements' => $departements,
        ]);
    }

    #[Route('/new', name: 'app_departement_new', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'app_departement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $departement = new Departement();
        $form = $this->createForm(DepartementType::class, $departement);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $date=new \DateTime();
            $departement->setCreatedAt($date);
            $departement->setUpdatedAt($date);
            $entityManager->persist($departement);
            $entityManager->flush();
            return $this->redirectToRoute('app_departement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('departement/new.html.twig', [
            'departement' => $departement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_departement_show', methods: ['GET'])]
    public function show(Departement $departement,DepartementRepository $repository,$id): Response
    {
        $departement=$repository->find($id);
        return $this->render('departement/show.html.twig', [
            'departement' => $departement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_departement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Departement $departement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DepartementType::class, $departement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $date=new \DateTime();
            $departement->setUpdatedAt($date);
            $entityManager->flush();
            return $this->redirectToRoute('app_departement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('departement/edit.html.twig', [
            'departement' => $departement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_departement_delete', methods: ['POST'])]
    public function delete(Request $request, Departement $departement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$departement->getId(), $request->request->get('_token'))) {
            $entityManager->remove($departement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_departement_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/activer/{id}', name: 'app_departement_activer')]
    public function activer(DepartementRepository $repository,$id,ManagerRegistry $doctrine){
        $departement=$repository->find($id);
        $departement->setEnabled(true);
        $em=$doctrine->getManager();
        $em->flush();
        return $this->redirectToRoute("app_departement_index");

    }
    #[Route('/desactiver/{id}', name: 'app_departement_desactiver')]
    public function desactiver(DepartementRepository $repository,$id,ManagerRegistry $doctrine){
        $departement=$repository->find($id);
        $departement->setEnabled(false);
        $em=$doctrine->getManager();
        $em->flush();
        return $this->redirectToRoute("app_departement_index");

    }

}
