<?php

namespace App\Controller;

use App\Repository\MinistereInnovationRepository;
use App\Repository\MinistereProfitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboard')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'dashboard')]
    public function index(MinistereInnovationRepository $innovRepo, MinistereProfitRepository $profitRepo): Response
    {
        $riskErreur = 0.001;
        if ($riskErreur > 0.001) {
            throw new \Exception('Risque d\'erreur trop eleve, simulation arretee !');
        }

        $innovations = $innovRepo->findBy([], ['ministere' => 'ASC']);
        $profits = $profitRepo->findAll();
        $totalProfit = array_sum(array_map(static fn($p) => $p->getProfit(), $profits));

        return $this->render('dashboard/index.html.twig', [
            'innovations' => $innovations,
            'profits' => $profits,
            'totalProfit' => $totalProfit,
        ]);
    }
}
