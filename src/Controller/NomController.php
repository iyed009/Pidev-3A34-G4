<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NomController extends AbstractController
{
    #[Route('/nom', name: 'app_nom')]
    public function index(): Response
    {
        return $this->render('nom/index.html.twig', [
            'controller_name' => 'NomController',
        ]);
    }
}
