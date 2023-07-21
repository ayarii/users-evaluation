<?php

namespace App\Controller;

use App\Repository\DepartementRepository;
use App\Repository\SessionRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
 * @Route("/dashboard")
 * 
 */
class DashboardController extends AbstractController
{
   
   public function index(UserRepository $repouser,SessionRepository $total): Response
    {
        $utilisateurs = $repouser->findAll();
        $nbutilisateurs = count($utilisateurs);
        $sessions = $total->findAll();
        $nsessions = count($sessions);
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'nbutilisateurs' => $nbutilisateurs,
            'nsessions' => $nsessions,

        ]);
    }

}


