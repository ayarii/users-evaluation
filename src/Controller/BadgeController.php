<?php

namespace App\Controller;

use App\Entity\Badge;
use App\Form\BadgeType;
use App\Repository\AffectationBadgeRepository;
use App\Repository\BadgeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/badge')]
class BadgeController extends AbstractController
{
    /**
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/', name: 'app_badge_index', methods: ['GET'])]
    public function index(BadgeRepository $badgeRepository): Response
    {
        return $this->render('badge/index.html.twig', [
            'badges' => $badgeRepository->findAll(),
        ]);
    }

    /**
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/new', name: 'app_badge_new', methods: ['GET', 'POST'])]
    public function new(Request $request, BadgeRepository $badgeRepository): Response
    {
        $badge = new Badge();
        $form = $this->createForm(BadgeType::class, $badge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $badge->setCreatedAt(new \DateTime());
            $badge->setUpdatedAt(new \DateTime());
            $badge->setEnabled(1);


            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
            $imageData = $form->get('libelle')->getData()  . '.' . $imageFile->guessExtension();
            $imageFile->move(
                $this->getParameter('badges_directory'),
                $imageData
            );
            $badge->setImage($imageData);
        }
            $badgeRepository->save($badge, true);
            $this->addFlash('success', 'Badge ajouté avec succés!');

            return $this->redirectToRoute('app_badge_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('badge/new.html.twig', [
            'badge' => $badge,
            'form' => $form,
        ]);
    }

    /**
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/{id}', name: 'app_badge_show', methods: ['GET'])]
    public function show(Badge $badge,AffectationBadgeRepository $affbadrepo): Response
    {
        $users= $affbadrepo->findUsersByBadgeId($badge->getId());
        return $this->render('badge/show.html.twig', [
            'badge' => $badge,
            'users'=> $users
        ]);
    }

    /**
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/{id}/edit', name: 'app_badge_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Badge $badge, BadgeRepository $badgeRepository): Response
    {
        $form = $this->createForm(BadgeType::class, $badge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $badge->setUpdatedAt(new \DateTime());

            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
            $imageData = $form->get('libelle')->getData() . '-' . $badge->getId() . '.' . $imageFile->guessExtension();
            $imageFile->move(
                $this->getParameter('badges_directory'),
                $imageData
            );
            $badge->setImage($imageData);
        }
            $badgeRepository->save($badge, true);
            $this->addFlash('success', 'Badge mise à jour avec succés!');
            return $this->redirectToRoute('app_badge_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('badge/edit.html.twig', [
            'badge' => $badge,
            'form' => $form,
        ]);
    }

    /*   #[Route('/{id}', name: 'app_badge_delete', methods: ['POST'])]
    public function delete(Request $request, Badge $badge, BadgeRepository $badgeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$badge->getId(), $request->request->get('_token'))) {
            $badgeRepository->remove($badge, true);
        }

        return $this->redirectToRoute('app_badge_index', [], Response::HTTP_SEE_OTHER);
    }*/

    /**
     * 
     * @IsGranted("ROLE_ADMIN")
     */

    #[Route('/activate/{id}', name: 'app_badge_activate', methods: ['GET'])]
    public function activate(Request $request, Badge $badge): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        //if ($this->isCsrfTokenValid('active' . $user->getId(), $request->request->get('_token'))) {
        $badge->setEnabled(1);
        $badge->setUpdatedAt(new \DateTime());
        $entityManager->persist($badge);
        $entityManager->flush();
        // }


        $this->addFlash('success', 'Badge activé avec succés!');
        return $this->redirectToRoute('app_badge_index', [], Response::HTTP_SEE_OTHER);
    }
    /**
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/desactivate/{id}', name: 'app_badge_desactivate', methods: ['GET'])]
    public function desactivate(Request $request, Badge $badge): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        //if ($this->isCsrfTokenValid('active' . $user->getId(), $request->request->get('_token'))) {
        $badge->setEnabled(0);
        $badge->setUpdatedAt(new \DateTime());
        $entityManager->persist($badge);
        $entityManager->flush();
        // }


        $this->addFlash('success', 'Badge bloqué avec succés!');
        return $this->redirectToRoute('app_badge_index', [], Response::HTTP_SEE_OTHER);
    }
}
