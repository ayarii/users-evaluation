<?php

namespace App\Controller;

use App\Entity\AffectationBadge;
use App\Form\AffectationBadgeType;
use App\Repository\AffectationBadgeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/affectation/badge')]
class AffectationBadgeController extends AbstractController
{
    #[Route('/', name: 'app_affectation_badge_index', methods: ['GET'])]
    public function index(AffectationBadgeRepository $affectationBadgeRepository): Response
    {
        return $this->render('affectation_badge/index.html.twig', [
            'affectation_badges' => $affectationBadgeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_affectation_badge_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $affectationBadge = new AffectationBadge();
        $form = $this->createForm(AffectationBadgeType::class, $affectationBadge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($affectationBadge);
            $entityManager->flush();

            return $this->redirectToRoute('app_affectation_badge_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('affectation_badge/new.html.twig', [
            'affectation_badge' => $affectationBadge,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_affectation_badge_show', methods: ['GET'])]
    public function show(AffectationBadge $affectationBadge): Response
    {
        return $this->render('affectation_badge/show.html.twig', [
            'affectation_badge' => $affectationBadge,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_affectation_badge_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AffectationBadge $affectationBadge, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AffectationBadgeType::class, $affectationBadge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_affectation_badge_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('affectation_badge/edit.html.twig', [
            'affectation_badge' => $affectationBadge,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_affectation_badge_delete', methods: ['POST'])]
    public function delete(Request $request, AffectationBadge $affectationBadge, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$affectationBadge->getId(), $request->request->get('_token'))) {
            $entityManager->remove($affectationBadge);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_affectation_badge_index', [], Response::HTTP_SEE_OTHER);
    }
}
