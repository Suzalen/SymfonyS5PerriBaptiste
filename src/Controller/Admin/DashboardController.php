<?php

namespace App\Controller\Admin;

use App\Repository\ClientRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_admin_dashboard')]
    public function index(
        UserRepository $userRepository,
        ProductRepository $productRepository,
        ClientRepository $clientRepository
    ): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'userCount' => $userRepository->count([]),
            'productCount' => $productRepository->count([]),
            'clientCount' => $clientRepository->count([]),
        ]);
    }
}
