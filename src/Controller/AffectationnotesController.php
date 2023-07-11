<?php

namespace App\Controller;

use App\Entity\Affectationnotes;
use App\Entity\User;

use App\Form\AffectationnotesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/affectationnotes')]
class AffectationnotesController extends AbstractController
{
    #[Route('/', name: 'app_affectationnotes_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    { 
        $affectationnotes = $entityManager
            ->getRepository(Affectationnotes::class)
            
            ->createQueryBuilder('u')

            ->where('u.enabled = :bool')
            ->setParameter('bool', 1)
           
            ->getQuery()
            ->getResult();
            

    

        return $this->render('affectationnotes/index.html.twig', [
            'affectationnotes' => $affectationnotes,
        ]);
    }

    #[Route('/new', name: 'app_affectationnotes_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $affectationnote = new Affectationnotes();
        $form = $this->createForm(AffectationnotesType::class, $affectationnote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $affectationnote->setEnabled(1);
            $entityManager->persist($affectationnote);
            $entityManager->flush();
            $this->addFlash('success', 'Affectation ajoutée avec succés!');
            return $this->redirectToRoute('app_affectationnotes_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('affectationnotes/new.html.twig', [
            'affectationnote' => $affectationnote,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_affectationnotes_show', methods: ['GET'])]
    public function show(Affectationnotes $affectationnote): Response
    {
        return $this->render('affectationnotes/show.html.twig', [
            'affectationnote' => $affectationnote,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_affectationnotes_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Affectationnotes $affectationnote, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AffectationnotesType::class, $affectationnote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Affectation modifiée  avec succés!');
            return $this->redirectToRoute('app_affectationnotes_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('affectationnotes/edit.html.twig', [
            'affectationnote' => $affectationnote,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_affectationnotes_delete', methods: ['POST'])]
    public function delete(Request $request, Affectationnotes $affectationnote, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$affectationnote->getId(), $request->request->get('_token'))) {
            $affectationnote->setEnabled(0);
            $entityManager->persist($affectationnote);
            $entityManager->flush();

        }

        return $this->redirectToRoute('app_affectationnotes_index', [], Response::HTTP_SEE_OTHER);
    }
}
