<?php

namespace App\Controller;

use App\Entity\Affectationnotes;
use App\Entity\Evaluation;
use App\Entity\User;
use App\Entity\Critere;
use App\Entity\Evaluation;

use App\Form\AffectationnotesType;
use App\Form\ChoiceEvType;
use App\Repository\CritereRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/affectationnotes')]
class AffectationnotesController extends AbstractController
{
    #[Route('/admin', name: 'app_affectationnotes_admin', methods: ['GET'])]
    public function indexAdmin(EntityManagerInterface $entityManager): Response
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
    #[Route('/', name: 'app_affectationnotes_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    { 
        $affectationnotes = $entityManager
            ->getRepository(Affectationnotes::class)
            
            ->createQueryBuilder('u')
            ->join(Critere::class,'c')
            ->join(Evaluation::class,'e')
            ->where('c.id=u.critere')
            ->andWhere('e.id=c.idEvaluation')
            ->andWhere('e.idUser = :id')
            ->setParameter('id', $this->getUser())
            ->andWhere('u.enabled = 1')
        
           
            
           
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
    #[Route('/choixEv', name: 'app_affectationnotes_ev', methods: ['GET', 'POST'])]
    public function ChoixEvaluation(Request $request, EntityManagerInterface $entityManager): Response
    {
        
        $form = $this->createForm(ChoiceEvType::class);
        $form->handleRequest($request);
       
        if ($form->isSubmitted() && $form->isValid()) {
           $eval= $form["eval"]->getData();
        //  dd($eval);
            return $this->redirectToRoute('app_affectationnotes_showUsers', ['id'=>$eval->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('affectationnotes/choixev.html.twig', [
           
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
    #[Route('/users/{id}', name: 'app_affectationnotes_showUsers', methods: ['GET', 'POST'])]
    public function showUsers(Evaluation $evaluation,EntityManagerInterface $entityManager,CritereRepository $critereRepository,UserRepository $userRepository,Request $request): Response
    {  $criteres= $critereRepository
        ->createQueryBuilder('u')

        ->where('u.enabled = :bool')
        ->setParameter('bool', 1)
        ->andWhere('u.idEvaluation = :id')
        ->setParameter('id', $evaluation->getId())
        ->getQuery()
        ->getResult();
        $users=$userRepository
        ->createQueryBuilder('u')

       
        ->where('u.roles LIKE :roles')
        ->setParameter('roles', '%"'."ROLE_Utilisateur".'"%')
        ->getQuery()
        ->getResult();
       
        
        $repo= $entityManager->getRepository(Affectationnotes::class);
        $aff = $repo->createQueryBuilder('p')
           
            ->getQuery()
            ->getResult();

        // Process the form data as per your requirements

        // Redirect or render a response
        

    
        return $this->render('affectationnotes/showUsers.html.twig', [
            'criteres' =>$criteres,
            'users'=> $users,
            
            
            
        ]);
    }
    #[Route('/submit/{id}/{userId}', name: 'app_affectationnotes_submit', methods: ['GET', 'POST'])]
    public function submit(Evaluation $evaluation,$userId,$id,Request $request, EntityManagerInterface $entityManager,CritereRepository $critereRepository,UserRepository $userRepository): Response
    {   //dd($request->request->get("userId"));
        
        $criteres= $critereRepository
        ->createQueryBuilder('u')

        ->where('u.enabled = :bool')
        ->setParameter('bool', 1)
        ->andWhere('u.idEvaluation = :id')
        ->setParameter('id', $evaluation->getId())
        ->getQuery()
        ->getResult();
        $users=$userRepository
        ->createQueryBuilder('u')

       
        ->where('u.roles LIKE :roles')
        ->setParameter('roles', '%"'."ROLE_Utilisateur".'"%')
        ->getQuery()
        ->getResult();
          

                  foreach($criteres as $cr){

               
               $affectation= $entityManager->getRepository(Affectationnotes::class)->findOneBy(['user' => $userId , 'critere'=>$cr->getId()]);
               if($affectation==null){ $aff = new Affectationnotes();
                $aff->setCritere($critereRepository->find($cr->getId()) );
                //dd($request->request->get($cr->getId().$userId));
                $aff->setNote(floatval($request->request->get($cr->getId().$userId)));
              // dd( ($request->request->get($cr->getId()."-".$userId)));
                $aff->setUser($userRepository->find($userId) );
                $aff->setEnabled(1);
                $entityManager->persist($aff);
                $entityManager->flush();}
                else{$affectation->setCritere($critereRepository->find($cr->getId()) );
                    //dd($request->request->get($cr->getId().$userId));
                    $affectation->setNote(floatval($request->request->get($cr->getId().$userId)));
                  // dd( ($request->request->get($cr->getId()."-".$userId)));
                    $affectation->setUser($userRepository->find($userId) );
                    $affectation->setEnabled(1);
                    $entityManager->persist($affectation);
                    $entityManager->flush();}
                //dd($aff);
               
            }
            return $this->json([
                'success' => true,
                'message' => 'Form submitted successfully!',
                // You can add any additional data you want to return to the client
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
    #[Route('/{userId}/{critereId}', name: 'app_affectationnotes_getAff', methods: ['GET'])]
    public function getAff(Request $request,$userId,$critereId, EntityManagerInterface $entityManager,NormalizerInterface $normalizer): Response
    {
        
        $repo= $entityManager->getRepository(Affectationnotes::class);
       $aff = $repo->findOneBy(['user' => $userId,'critere' => $critereId]);
        // ->createQueryBuilder('u')

        // ->where('u.enabled = :bool')
        // ->setParameter('bool', 1)
        // ->andWhere('u.user = :id')
        // ->setParameter('id',$userId)
        // ->andWhere('u.critere = :id')
        // ->setParameter('id',$critereId)
        // ->getQuery()
        // ->getResult();
        $AffNormalises = $normalizer->normalize($aff, 'json', ['affectation' => "affectation"]);
        return new Response(json_encode($AffNormalises));
    }

}
