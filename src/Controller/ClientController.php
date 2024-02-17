<?php

namespace App\Controller;

use App\Repository\ReclamationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{


    #[Route('/client', name: 'app_reclamation_client', methods: ['GET'])]
    public function index1(ReclamationRepository $reclamationRepository): Response
    {
        return $this->render('client/index.html.twig', [
            'reclamations' => $reclamationRepository->findAll(),
        ]);
    }
}
