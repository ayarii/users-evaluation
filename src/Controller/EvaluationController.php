<?php

namespace App\Controller;

use App\Entity\Affectationnotes;
use App\Entity\Critere;
use App\Entity\Departement;
use App\Entity\Evaluation;
use App\Entity\Session;
use App\Entity\User;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Form\EvaluationType;
use App\Repository\CritereRepository;
use App\Repository\DepartementRepository;
use App\Repository\UserRepository;
use App\Repository\EvaluationRepository;
use App\Repository\SessionRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Dompdf\Dompdf;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/evaluation')]
class EvaluationController extends AbstractController
{
    #[Route('/', name: 'app_evaluation_index', methods: ['GET'])]
    public function index(EvaluationRepository $evaluationRepository,UserRepository $userRepository): Response
    { 
        return $this->render('evaluation/index.html.twig', [
            'evaluations' => $evaluationRepository
            ->createQueryBuilder('u')

            
            ->andWhere('u.idUser = :id')
            ->setParameter('id', $this->getUser())
            ->getQuery()
            ->getResult(), 
            'standard'=> false
        ]);
    }
    #[Route('/standard', name: 'app_evaluation_standard', methods: ['GET'])]
    public function standard(EvaluationRepository $evaluationRepository,UserRepository $userRepository): Response
    { $admin= $userRepository
        ->createQueryBuilder('u')

       
        ->where('u.roles LIKE :roles')
        ->setParameter('roles', '%"'."ROLE_ADMIN".'"%')
        ->getQuery()
        ->getResult();
       
        return $this->render('evaluation/index.html.twig', [
            'evaluations' => $evaluationRepository
            ->createQueryBuilder('u')

            ->where('u.enabled = :bool')
            ->setParameter('bool', 1)
            ->andWhere('u.idUser = :id')
            ->setParameter('id', $admin[0])
            ->getQuery()
            ->getResult(),
           
            'standard'=> true
        ]);
    }
    #[Route('/admin', name: 'app_evaluation_admin', methods: ['GET'])]
    public function adminlist(EvaluationRepository $evaluationRepository): Response
    {
        return $this->render('evaluation/index.html.twig', [
            'evaluations' => $evaluationRepository
            ->findAll(),
            'standard'=>false
        ]);
    }


    #[Route('/new', name: 'app_evaluation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EvaluationRepository $evaluationRepository,UserRepository $userRepository): Response
    {
        $evaluation = new Evaluation();
        $form = $this->createForm(EvaluationType::class, $evaluation);
       $user=new User();
        $user=$userRepository->find($this->getUser());
        $evaluation->setIdUser($user);
        $evaluation->setIdDepartement($user->getIdDepartement());
        $evaluation->setEnabled(1);
        $evaluation->setCreatedAt(new DateTime());
        $evaluation->setUpdatedAt(new DateTime());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $evaluationRepository->save($evaluation, true);
            $this->addFlash('success', 'Evaluation ajoutée avec succés!');
            if($this->getUser()->getRoles()[0] =="ROLE_GESTIONNAIRE"){
              return $this->redirectToRoute('app_evaluation_index', [], Response::HTTP_SEE_OTHER);  
            }else{
                return $this->redirectToRoute('app_evaluation_admin', [], Response::HTTP_SEE_OTHER);
            }
            
        }

        return $this->renderForm('evaluation/new.html.twig', [
            'evaluation' => $evaluation,
            'form' => $form,
        ]);
    }
    #[Route('/{idEv}/{idUser}', name: 'app_evaluation_showDetails', methods: ['GET'])]
    public function showDetailsForAdmin($idEv,$idUser,EntityManagerInterface $entityManager,EvaluationRepository $evRepo,CritereRepository $critereRepository,UserRepository $userRepository): Response
    {
       
        $repo= $entityManager->getRepository(Affectationnotes::class);
        $criteres= $critereRepository
        ->createQueryBuilder('u')

        ->where('u.enabled = :bool')
        ->setParameter('bool', 1)
        ->andWhere('u.idEvaluation = :id')
        ->setParameter('id', $idEv)
        ->getQuery()
        ->getResult();
        $user=$userRepository
        ->find($idUser);
        
        $aff = $repo->createQueryBuilder('p')
        ->andWhere('p.user = :id')
        ->setParameter('id', $idUser)
           
            ->getQuery()
            ->getResult();
           $evaluation= $evRepo->find($idEv);
        return $this->render('affectationnotes/show.html.twig', [
            'evaluation' => $evaluation,
            'user'=>$user,
            'criteres'=>$criteres,
            'affectations'=>$aff,
            
            
        ]);
    }
    #[Route('/show/details/{id}', name: 'app_evaluation_showEv', methods: ['GET'])]
    public function show($id,EntityManagerInterface $entityManager,EvaluationRepository $evRepo,CritereRepository $critereRepository,UserRepository $userRepository): Response
    {
         $repo= $entityManager->getRepository(Affectationnotes::class);
        $affe = $repo
        ->createQueryBuilder('an')

        ->select('an, SUM(an.note) AS totalNote')
       
        ->join(Critere::class,'c')
        ->where('an.critere = c.id')
        ->andWhere('c.idEvaluation = :evaluationId')
         ->andWhere('an.gestionnaire = :ges')
        ->setParameter('ges', $this->getUser())
        ->groupBy('an.user')
        ->orderBy('totalNote', 'DESC')
        ->setMaxResults(3)
        ->setParameter('evaluationId', $id)

            ->getQuery()
            ->getResult();
        
        $criteres= $critereRepository
        ->createQueryBuilder('u')

        ->where('u.enabled = :bool')
        ->setParameter('bool', 1)
        ->andWhere('u.idEvaluation = :id')
        ->setParameter('id', $id)
        ->getQuery()
        ->getResult();
        $users=$userRepository
        ->createQueryBuilder('u')
        ->andWhere('u.idGroupe = :id')
        ->setParameter('id', $userRepository->find($this->getUser())->getIdGroupe())
       
        ->andWhere('u.roles LIKE :roles')
        ->setParameter('roles', '%"'."ROLE_Utilisateur".'"%')
        ->getQuery()
        ->getResult();
       
        
        $aff = $repo->createQueryBuilder('p')
        ->andWhere('p.gestionnaire = :ges')
        ->setParameter('ges', $this->getUser())
           
            ->getQuery()
            ->getResult();
           $evaluation= $evRepo->find($id);
        return $this->render('evaluation/show.html.twig', [
            'evaluation' => $evaluation,
            'users'=>$users,
            'criteres'=>$criteres,
            'affectations'=>$aff,
            'topAffs'=>$affe
            
        ]);
    }
    #[Route('/stat', name: 'app_evaluation_stats', methods: ['GET'])]
    public function Stats(EntityManagerInterface $entityManager,CritereRepository $critereRepository,UserRepository $userRepository): Response
    { $repo= $entityManager->getRepository(Session::class);
        $sessions = $repo
        ->createQueryBuilder('sess')
       
            ->getQuery()
            ->getResult();
      
       
        return $this->render('evaluation/statCritere.html.twig', [
            'sessions' => $sessions,
           
            
        ]);
    }

    #[Route('/getDep', name: 'app_evaluation_dep', methods: ['GET'])]
    public function getDep(DepartementRepository $depRepository,Request $request,NormalizerInterface $normalizer): Response
    {
        
        $parentId = $request->query->get('parentId');
          $childDropdownOptions = $depRepository
        ->createQueryBuilder('dep')
       ->where('dep.idSession = :id')
       ->setParameter('id',$parentId)
            ->getQuery()
            ->getResult();
            $AffNormalises = $normalizer->normalize($childDropdownOptions, 'json', ['childDropdownOptions' => "childDropdownOptions"]);
            return new Response(json_encode($AffNormalises));

    }
    #[Route('/getEv', name: 'app_evaluation_getEv', methods: ['GET'])]
    public function getEv(EntityManagerInterface $entityManager,Request $request,NormalizerInterface $normalizer): Response
    {
        
        $parentId = $request->query->get('parentId');
          $repo= $entityManager->getRepository(Evaluation::class);
          $childDropdownOptions = $repo
        ->createQueryBuilder('ev')
        ->join(User::class,'u')
       ->where('ev.idUser = u.id')
       ->andWhere('u.idDepartement = :id')
       ->setParameter('id',$parentId)
            ->getQuery()
            ->getResult();
            $AffNormalises = $normalizer->normalize($childDropdownOptions, 'json', ['childDropdownOptions' => "childDropdownOptions"]);
            return new Response(json_encode($AffNormalises));
     
    }
    #[Route('/getCr', name: 'app_evaluation_getCr', methods: ['GET'])]
    public function getCr(CritereRepository $critereRepository,Request $request,NormalizerInterface $normalizer): Response
    {
        
        $parentId = $request->query->get('parentId');
          $childDropdownOptions = $critereRepository
        ->createQueryBuilder('cr')
       
       ->where('cr.idEvaluation = :id')
       ->setParameter('id',$parentId)
            ->getQuery()
            ->getResult();
            $AffNormalises = $normalizer->normalize($childDropdownOptions, 'json', ['childDropdownOptions' => "childDropdownOptions"]);
            return new Response(json_encode($AffNormalises));
     
    }
    #[Route('/getCrStat', name: 'app_evaluation_getCrStat', methods: ['GET'])]
    public function getCrStat(EntityManagerInterface $entityManager,CritereRepository $critereRepository,UserRepository $userRepository,Request $request,NormalizerInterface $normalizer): Response
    {
        
        $parentId = $request->query->get('parentId');
          $repo= $entityManager->getRepository(Affectationnotes::class);
         $nbA = $repo
        ->createQueryBuilder('aff')
        ->select('Count(aff.user) AS nbA')
       ->where('aff.critere = :id')
       ->setParameter('id',$parentId)
       ->join(Critere::class,'cr')
       ->andWhere('aff.critere = cr.id')
       ->andWhere('(cr.ponderation / 2 ) > aff.note')
      
            ->getQuery()
            ->getResult();
            $nbF = $repo
            ->createQueryBuilder('aff')
            ->select('Count(aff.user) AS nbA')
           ->where('aff.critere = :id')
           ->setParameter('id',$parentId)
           ->join(Critere::class,'cr')
           ->andWhere('aff.critere = cr.id')
           ->andWhere('(cr.ponderation / 2 ) <= aff.note')
          
                ->getQuery()
                ->getResult();
           
                $response = [
                    'nbA' => $nbA,
                    'nbF' => $nbF
                ];
                return new JsonResponse($response);
     
    }
    #[Route('/{id}/toexcel', name: 'app_evaluation_excel', methods: ['GET', 'POST'])]
    public function toExcel(EntityManagerInterface $entityManager,Request $request, Evaluation $evaluation, CritereRepository $critereRepository,UserRepository $userRepository,EvaluationRepository $evaluationRepository): Response
    {
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

        ->andWhere('u.idGroupe = :id')
        ->setParameter('id', $userRepository->find($this->getUser())->getIdGroupe())
        ->andWhere('u.roles LIKE :roles')
        ->setParameter('roles', '%"'."ROLE_Utilisateur".'"%')
        ->getQuery()
        ->getResult();
       
        
        $repo= $entityManager->getRepository(Affectationnotes::class);
        $affectations = $repo->createQueryBuilder('p')
           
            ->getQuery()
            ->getResult();
      
       
        $totalPon=0;
        $csvData = array();
        $headerRow = array('Utilisateur');
        foreach ($criteres as $critere) {
            $headerRow[] = $critere->getLibelle() . ' / ' . $critere->getPonderation();
            $totalPon=$totalPon+ $critere->getPonderation();
        }
       
        $headerRow[] = 'SCORE TOTAL / ' . $totalPon;
        $csvData[] = $headerRow;
    
        foreach ($users as $user) {
            $rowData = array();
            $rowData[] = $user->getNom() . ' ' . $user->getPrenom();
            $total = 0;
            foreach ($criteres as $critere) {
                $note = 0;
                foreach ($affectations as $affectation) {
                    if ($affectation->getUser()->getId() == $user->getId() && $affectation->getGestionnaire() == $this->getUser() && $affectation->getCritere()->getId() == $critere->getId()) {
                        $note = $affectation->getNote();
                        break;
                    }
                }
                $rowData[] = $note;
                $total += $note;
            }
            $rowData[] = $total;
            $csvData[] = $rowData;
        }
    
        $csvContent = '';
        foreach ($csvData as $row) {
            $csvContent .= implode(',', $row) . "\n";
        }
    
        $response = new Response($csvContent);
    
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="table_export.csv"');
    
        return $response;
    }
    #[Route('/{userId}/{evId}/toPdf', name: 'app_evaluation_pdf', methods: ['GET', 'POST'])]
    public function toPDF(Request $request,$userId,$evId,EntityManagerInterface $entityManager,UserRepository $userRepository,CritereRepository $critereRepository, EvaluationRepository $evaluationRepository,DepartementRepository $depRepo,SessionRepository $sessRepo): Response
    { //  dd($userId,$evId);
         $criteres= $critereRepository
        ->createQueryBuilder('u')

        ->where('u.enabled = :bool')
        ->setParameter('bool', 1)
        ->andWhere('u.idEvaluation = :id')
        ->setParameter('id', $evId)
        ->getQuery()
        ->getResult();
        $evaluation= $evaluationRepository->find($evId);
      
        $user=$userRepository
        ->find($userId);
        
      
        $repo= $entityManager->getRepository(Affectationnotes::class);
        $affectations = $repo->createQueryBuilder('u')
       
      
        ->andWhere('u.user = :id')
        ->setParameter('id', $userId)
        
        ->getQuery()
        ->getResult();
        $html = $this->renderView('/evaluation/pdf_template.html.twig', ['affectations' => $affectations,'user'=>$user,'criteres'=>$criteres,'evaluation'=>$evaluation]);


       
        $dompdf = new Dompdf();

        

        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        $pdfOutput = $dompdf->output();

        $response = new Response($pdfOutput);

        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="output.pdf"');

        return $response;
    }
    #[Route('/{id}/edit/modif', name: 'app_evaluation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evaluation $evaluation, EvaluationRepository $evaluationRepository): Response
    {
        $form = $this->createForm(EvaluationType::class, $evaluation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $evaluation->setUpdatedAt(new DateTime());
            $evaluationRepository->save($evaluation, true);
            $this->addFlash('success', 'Evaluation modifiée avec succés!');
            return $this->redirectToRoute('app_evaluation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evaluation/edit.html.twig', [
            'evaluation' => $evaluation,
            'form' => $form,
        ]);
    }

  
    #[Route('/{id}', name: 'app_evaluation_delete', methods: ['POST','GET'])]
    public function delete(Request $request, Evaluation $ev, EntityManagerInterface $entityManager): Response
    {
        $ev->setEnabled(0);
            $entityManager->persist($ev);
           
            $entityManager->flush();
        

            return $this->redirectToRoute("app_evaluation_index");

    }
    #[Route('/activer/act/{id}', name: 'app_evaluation_activer', methods: ['POST','GET'])]
    public function activer(Request $request, Evaluation $ev, EntityManagerInterface $entityManager): Response
    {
       
            $ev->setEnabled(1);
            $entityManager->persist($ev);
            $entityManager->flush();
        

            return $this->redirectToRoute("app_evaluation_index");

    }
}
