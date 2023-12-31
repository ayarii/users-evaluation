<?php

namespace App\Controller;


use App\Entity\AffectationBadge;
use App\Repository\AffectationBadgeRepository;
use App\Repository\BadgeRepository;
use App\Repository\DepartementRepository;
use Symfony\Component\HttpFoundation\Request;

use App\Repository\SessionRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/dashboard")
 * 
 */
class DashboardController extends AbstractController
{
   
     /**
     * 
     * @IsGranted("ROLE_ADMIN")
     */

   public function index(UserRepository $repouser,SessionRepository $total,AffectationBadgeRepository $repoaff,SessionRepository $repoSess,BadgeRepository $repobadge): Response
    {    $sessions = $repoSess

        ->createQueryBuilder('sess')
       
            ->getQuery()
            ->getResult();
      

        $utilisateurs = $repouser->findAll();
        $nbutilisateurs = count($utilisateurs);
        $sessions = $total->findAll();
        $nsessions = count($sessions);
        $badges = $repobadge->findAll();
        $nbadges = count($badges);

        $usersData = [];
        $usersData = $this->getchartUsersPerDepatments($repouser);

     

        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'nbutilisateurs' => $nbutilisateurs,
            'nbbadges' => $nbadges,

            'nsessions' => $nsessions,
            'usersData' => $usersData,
            
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


