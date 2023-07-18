<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditProfileType;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\DefaultImageGenerator as ServiceDefaultImageGenerator;
use App\Service\SendMailService;
use AppBundle\Service\DefaultImageGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

#[Route('/user')]


class UserController extends AbstractController
{
    /**
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository, UserPasswordEncoderInterface $userPasswordHasher, SendMailService $mail, ServiceDefaultImageGenerator $defaultImageGenerator): Response
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
                $imageData = $form->get('nom')->getData() . '-' . $form->get('prenom')->getData() . $form->get('id')->getData() . '.' . $imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('users_directory'),
                    $imageData
                );
                $user->setImage($imageData);
            } else {
                $user->setImage("");
                $defaultImageGenerator->generateImage($form->get('nom')->getData(), $form->get('prenom')->getData(), $form->get('id')->getData());

                $imageData = $form->get('nom')->getData() . '-' . $form->get('prenom')->getData()  . $form->get('id')->getData() . '.png';
                $user->setImage($imageData);
            }

            $user->setPassword($userPasswordHasher->encodePassword(
                $user,
                $form->get('password')->getData()
            ));
            $user->setId($id);
            $user->setEnabled(1);
            //dd($user);
            $userRepository->save($user, true);
            $password = $form->get('password')->getData();
            $context = compact('user', 'password');
            $mail->send(

                $user->getEmail(),
                'Création de compte!',
                'emailAddUser',
                $context
            );

            $this->addFlash('success', 'Utilisateur ajouté avec succés!');
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }


    #[Route('/profile/{id}', name: 'app_user_profile', methods: ['GET', 'POST'])]
    public function profile(Request $request, User $user, ServiceDefaultImageGenerator $defaultImageGenerator): Response
    {

        $form = $this->createForm(EditProfileType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle form submission and update the entity
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $imageData = $form->get('nom')->getData() . '-' . $form->get('prenom')->getData() . $form->get('id')->getData() . '.' . $imageFile->guessExtension();

                $imageFile->move(
                    $this->getParameter('users_directory'),
                    $imageData
                );
                $user->setImage($imageData);
            }

            // Persist changes to the database
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash('success', 'Votre profil à été mise à jour avec succés!');
            return $this->redirectToRoute('app_user_profile', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }
        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository, UserPasswordEncoderInterface $userPasswordHasher, SendMailService $mail, ServiceDefaultImageGenerator $defaultImageGenerator): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $imageData = $form->get('nom')->getData() . '-' . $form->get('prenom')->getData() . $form->get('id')->getData() . '.' . $imageFile->guessExtension();

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
            $password = $form->get('password')->getData();
            $context = compact('password', 'user');
            $mail->send(

                $user->getEmail(),
                'Modification de compte !',
                'emailUpdateUser',
                $context
            );
            $userRepository->save($user, true);
            $this->addFlash('success', 'Utilisateur mise a jour avec succés!');
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    /*  #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository, SendMailService $mail): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $user->setEnabled(0);
            $entityManager->persist($user);
            $entityManager->flush();
        }
        $context = compact('user');
        $mail->send(

            $user->getEmail(),
            'Blockage de compte !',
            'emailBlockUser',
            $context
        );

        $this->addFlash('success', 'Utilisateur bloqué avec succés!');
        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }*/

    /**
     * 
     * @IsGranted("ROLE_ADMIN")
     */

    #[Route('/activate/{id}', name: 'app_user_activate', methods: ['GET'])]
    public function activate(Request $request, User $user, UserRepository $userRepository, SendMailService $mail): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        //if ($this->isCsrfTokenValid('active' . $user->getId(), $request->request->get('_token'))) {
        $user->setEnabled(1);
        $entityManager->persist($user);
        $entityManager->flush();
        // }
        $context = compact('user');
        $mail->send(

            $user->getEmail(),
            'Activation de compte !',
            'emailActivateUser',
            $context
        );

        $this->addFlash('success', 'Utilisateur activé avec succés!');
        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
    
    /**
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/desactivate/{id}', name: 'app_user_desactivate', methods: ['GET'])]
    public function desactivate(Request $request, User $user, UserRepository $userRepository, SendMailService $mail): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        //if ($this->isCsrfTokenValid('active' . $user->getId(), $request->request->get('_token'))) {
        $user->setEnabled(0);
        $entityManager->persist($user);
        $entityManager->flush();
        // }
        $context = compact('user');
        $mail->send(

            $user->getEmail(),
            'Blockage de compte !',
            'emailBlockUser',
            $context
        );

        $this->addFlash('success', 'Utilisateur bloqué avec succés!');
        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
