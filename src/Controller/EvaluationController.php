<?php

namespace App\Controller;

use App\Entity\Affectationnotes;
use App\Entity\Critere;
use App\Entity\Evaluation;
use App\Entity\User;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Form\EvaluationType;
use App\Repository\CritereRepository;
use App\Repository\UserRepository;
use App\Repository\EvaluationRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Dompdf\Dompdf;
#[Route('/evaluation')]
class EvaluationController extends AbstractController
{
    #[Route('/', name: 'app_evaluation_index', methods: ['GET'])]
    public function index(EvaluationRepository $evaluationRepository): Response
    {
        return $this->render('evaluation/index.html.twig', [
            'evaluations' => $evaluationRepository
            ->createQueryBuilder('u')

            ->where('u.enabled = :bool')
            ->setParameter('bool', 1)
            ->andWhere('u.idUser = :id')
            ->setParameter('id', $this->getUser())
            ->getQuery()
            ->getResult()
        ]);
    }
    #[Route('/admin', name: 'app_evaluation_admin', methods: ['GET'])]
    public function adminlist(EvaluationRepository $evaluationRepository): Response
    {
        return $this->render('evaluation/index.html.twig', [
            'evaluations' => $evaluationRepository
            ->findAll()
        ]);
    }


    #[Route('/new', name: 'app_evaluation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EvaluationRepository $evaluationRepository,UserRepository $userRepository): Response
    {
        $evaluation = new Evaluation();
        $form = $this->createForm(EvaluationType::class, $evaluation);
       // $evaluation->setIdUser($userRepository->find("JDKSNCJ"));
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
            return $this->redirectToRoute('app_evaluation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evaluation/new.html.twig', [
            'evaluation' => $evaluation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evaluation_show', methods: ['GET'])]
    public function show(Evaluation $evaluation,EntityManagerInterface $entityManager,CritereRepository $critereRepository,UserRepository $userRepository): Response
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
        return $this->render('evaluation/show.html.twig', [
            'evaluation' => $evaluation,
            'users'=>$users,
            'criteres'=>$criteres,
            'affectations'=>$aff,
            
        ]);
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

       
        ->where('u.roles LIKE :roles')
        ->setParameter('roles', '%"'."ROLE_Utilisateur".'"%')
        ->getQuery()
        ->getResult();
       
        
        $repo= $entityManager->getRepository(Affectationnotes::class);
        $affectations = $repo->createQueryBuilder('p')
           
            ->getQuery()
            ->getResult();
      
       
        $totalPon=0;
        // Populate the table headers
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
                  // dd( $affectation->getUser());
                    if ($affectation->getUser()->getId() == $user->getId() && $affectation->getCritere()->getId() == $critere->getId()) {
                        $note = $affectation->getNote();
                       // dd($note);
                        break;
                    }
                }
                $rowData[] = $note;
                $total += $note;
            }
            $rowData[] = $total;
            $csvData[] = $rowData;
        }
    
        // Create the CSV content as a string
        $csvContent = '';
        foreach ($csvData as $row) {
            $csvContent .= implode(',', $row) . "\n";
        }
    
        // Create a Response object with the CSV content
        $response = new Response($csvContent);
    
        // Set appropriate headers for CSV download
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="table_export.csv"');
    
        return $response;
    }
    #[Route('/{userId}/{evId}/toPdf', name: 'app_evaluation_pdf', methods: ['GET', 'POST'])]
    public function toPDF(Request $request,$userId,$evId,EntityManagerInterface $entityManager,UserRepository $userRepository,CritereRepository $critereRepository, EvaluationRepository $evaluationRepository): Response
    {    $criteres= $critereRepository
        ->createQueryBuilder('u')

        ->where('u.enabled = :bool')
        ->setParameter('bool', 1)
        ->andWhere('u.idEvaluation = :id')
        ->setParameter('id', $evId)
        ->getQuery()
        ->getResult();
        $user=$userRepository
        ->find($userId);
        $repo= $entityManager->getRepository(Affectationnotes::class);
        $affectations = $repo->createQueryBuilder('u')
        ->andWhere('u.user = :id')
        ->setParameter('id', $userId)
        ->getQuery()
        ->getResult();
          // dd($affectations);
      
        $html = $this->renderView('/evaluation/pdf_template.html.twig', ['affectations' => $affectations,'user'=>$user,'criteres'=>$criteres,'id'=>$evId]);

        // Create a new Dompdf instance
        $dompdf = new Dompdf();

        // Load the HTML content into Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Set the PDF options (e.g., paper size, orientation, etc.)
        $dompdf->setPaper('A4', 'portrait');

        // Render the PDF
        $dompdf->render();

        // Get the generated PDF content
        $pdfOutput = $dompdf->output();

        // Create a Response object with the PDF content
        $response = new Response($pdfOutput);

        // Set appropriate headers for PDF download
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="output.pdf"');

        return $response;
    }
    #[Route('/{id}/edit', name: 'app_evaluation_edit', methods: ['GET', 'POST'])]
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

    #[Route('/{id}', name: 'app_evaluation_delete', methods: ['POST'])]
    public function delete(Request $request, Evaluation $evaluation, EvaluationRepository $evaluationRepository): Response
    {  $entityManager = $this->getDoctrine()->getManager();
        if ($this->isCsrfTokenValid('delete'.$evaluation->getId(), $request->request->get('_token'))) {
            $evaluation->setEnabled(0);
            $entityManager->persist($evaluation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_evaluation_index', [], Response::HTTP_SEE_OTHER);
    }
}
