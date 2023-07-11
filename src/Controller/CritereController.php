<?php

namespace App\Controller;

use App\Entity\Critere;
use App\Form\CritereType;
use App\Repository\CritereRepository;

use DateTime;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/critere')]
class CritereController extends AbstractController
{

    #[Route('/evaluation/{id}', name: 'app_critere_index', methods: ['GET'])]
    public function index($id,CritereRepository $critereRepository): Response
    {
        return $this->render('critere/index.html.twig', [
            'criteres' => $critereRepository
            ->createQueryBuilder('u')

            ->where('u.enabled = :bool')
            ->setParameter('bool', 1)
            ->andWhere('u.idEvaluation = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult()

        ]);
    }

    #[Route('/new', name: 'app_critere_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CritereRepository $critereRepository): Response
    {
        $critere = new Critere();
        $form = $this->createForm(CritereType::class, $critere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
<
            $critere->setCreatedAt(new DateTime());
            $critere->setUpdatedAt(new DateTime());
            $critere->setEnabled(1);
            $critereRepository->save($critere, true);
            $this->addFlash('success', 'Critere ajouté avec succés!');
            return $this->redirectToRoute('app_critere_index', ['id'=>$critere->getIdEvaluation()->getId()], Response::HTTP_SEE_OTHER);

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
            $critere->setUpdatedAt(new DateTime());
            $critereRepository->save($critere, true);

            return $this->redirectToRoute('app_critere_index', ['id'=>$critere->getIdEvaluation()->getId()], Response::HTTP_SEE_OTHER);
        }
       // dd($critere->getIdEvaluation()->getId());

        return $this->renderForm('critere/edit.html.twig', [
            'critere' => $critere,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_critere_delete', methods: ['POST'])]
    public function delete(Request $request, Critere $critere, CritereRepository $critereRepository): Response
    {  $entityManager = $this->getDoctrine()->getManager();
        if ($this->isCsrfTokenValid('delete'.$critere->getId(), $request->request->get('_token'))) {
            $critere->setEnabled(0);
            $entityManager->persist($critere);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_critere_index', ['id'=>$critere->getIdEvaluation()->getId()], Response::HTTP_SEE_OTHER);
        }

}