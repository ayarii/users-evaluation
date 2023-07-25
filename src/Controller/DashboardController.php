<?php

namespace App\Controller;


use App\Entity\AffectationBadge;
use App\Repository\AffectationBadgeRepository;
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
   

   public function index(UserRepository $repouser,SessionRepository $total,AffectationBadgeRepository $repoaff,SessionRepository $repoSess): Response
    {    $sessions = $repoSess

        ->createQueryBuilder('sess')
       
            ->getQuery()
            ->getResult();
      

        $utilisateurs = $repouser->findAll();
        $nbutilisateurs = count($utilisateurs);
        $sessions = $total->findAll();
        $nsessions = count($sessions);

        $usersData = [];
        $usersData = $this->getchartUsersPerDepatments($repouser);

        $badgesData =[];
        $badgesData = $this->getchartUsersPerBadges($repoaff);

        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'nbutilisateurs' => $nbutilisateurs,

            'nsessions' => $nsessions,
            'usersData' => $usersData,
            'badgesData' => $badgesData,
            'sessions'=>$sessions


        ]);
    }


    public function getchartUsersPerDepatments(UserRepository $repouser)
    {
        $usersData = $repouser->findUsersCountPerDepartment();

        $chartData = [
            'labels' => [],
            'data' => [],
            'colors' => []
        ];
        $totalDepartments = 0;
        foreach ($usersData as $data) {
            $totalDepartments += $data['userCount'];
        }

        foreach ($usersData as $data) {
            $chartData['labels'][] = $data['libelle'];
            $chartData['data'][] = $data['userCount'];
            $chartData['colors'][] = $this->getRandomColor();
        }

        return $chartData;
    }


    public function getchartUsersPerBadges(AffectationBadgeRepository $repouser)
    {
        $badgesData = $repouser->CountByBadgeForSession('session2023');

        $chartData = [];
        $totalBadges = 0;
        foreach ($badgesData as $data) {
            $totalBadges += $data['count_users'];
        }
        

        foreach ($badgesData as $data) {
            $chartData[$data['badge_libelle']] = $data['count_users'];
           
        }

        return $chartData;
    }



    private function getRandomColor()
    {
        $letters = '0123456789ABCDEF';
        $color = '#';
        for ($i = 0; $i < 6; $i++) {
            $color .= $letters[rand(0, 15)];
        }
        return $color;
    }

}


