<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository, UserPasswordEncoderInterface $userPasswordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        //dd($user);
        if ($form->isSubmitted() && $form->isValid()) {
            //dd($user);
            $id = $form->get('id')->getData();
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $imageData = $user->getNom().'-'.$user->getPrenom().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('users_directory'),
                    $imageData
                );
                $user->setImage($imageData);
            }
            $user->setPassword($userPasswordHasher->encodePassword(
                $user,
                $form->get('password')->getData()
            ));
            $user->setId($id);
            //dd($user);
            $userRepository->save($user, true);
            $this->addFlash('success', 'Utilisateur ajouté avec succés!');
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository, UserPasswordEncoderInterface $userPasswordHasher): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $imageData =$user->getNom().'-'.$user->getPrenom().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('users_directory'),
                    $imageData
                );
                $user->setImage($imageData);
            }
           

            $user->setPassword($userPasswordHasher->encodePassword(
                $user,
                $form->get('password')->getData()
            ));
            //dd($user);
            $userRepository->save($user, true);
            $this->addFlash('success', 'Utilisateur mise a jour avec succés!');
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }
        $this->addFlash('success', 'Utilisateur supprimé avec succés!');
        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
