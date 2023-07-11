<?php

namespace App\Controller;

use App\Entity\Critere;
use App\Form\CritereType;
use App\Repository\CritereRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/critere')]
class CritereController extends AbstractController
{
    #[Route('/', name: 'app_critere_index', methods: ['GET'])]
    public function index(CritereRepository $critereRepository): Response
    {
        return $this->render('critere/index.html.twig', [
            'criteres' => $critereRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_critere_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CritereRepository $critereRepository): Response
    {
        $critere = new Critere();
        $form = $this->createForm(CritereType::class, $critere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $critereRepository->save($critere, true);

            return $this->redirectToRoute('app_critere_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('critere/new.html.twig', [
            'critere' => $critere,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_critere_show', methods: ['GET'])]
    public function show(Critere $critere): Response
    {
        return $this->render('critere/show.html.twig', [
            'critere' => $critere,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_critere_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Critere $critere, CritereRepository $critereRepository): Response
    {
        $form = $this->createForm(CritereType::class, $critere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $critereRepository->save($critere, true);

            return $this->redirectToRoute('app_critere_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('critere/edit.html.twig', [
            'critere' => $critere,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_critere_delete', methods: ['POST'])]
    public function delete(Request $request, Critere $critere, CritereRepository $critereRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$critere->getId(), $request->request->get('_token'))) {
            $critereRepository->remove($critere, true);
        }

        return $this->redirectToRoute('app_critere_index', [], Response::HTTP_SEE_OTHER);
    }
}
