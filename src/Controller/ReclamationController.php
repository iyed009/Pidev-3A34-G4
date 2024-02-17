<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\Reponse;
use App\Form\ReclamationType;
use App\Form\ReponseType;
use App\Repository\ReclamationRepository;
use App\Repository\ReponseRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Util\ClassUtils;


#[Route('/reclamation')]
class ReclamationController extends AbstractController
{
    #[Route('/', name: 'app_reclamation_index', methods: ['GET'])]
    public function index(ReclamationRepository $reclamationRepository): Response
    {
        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager ,UserRepository $utilisateurRepository): Response
    {
        $reclamation = new Reclamation();
        $user=$utilisateurRepository->find(1);
        $reclamation->setUtilisateur($user);
        $reclamation->setNumTele(7777777);
        $reclamation->setEmail('aaaaaaaa');

        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reclamation);
            $entityManager->flush();


            return $this->redirectToRoute('app_reclamation_showClient', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reclamation/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }



    #[Route('/{id}', name: 'app_reclamation_show', methods: ['GET'])]

     public function show(Reclamation $reclamation, Request $request, EntityManagerInterface $entityManager): Response
     {
         $reponse = new Reponse();
         $formReponse = $this->createForm(ReponseType::class, $reponse);
         $formReponse->handleRequest($request);

        if ($formReponse->isSubmitted() && $formReponse->isValid()) {
             $entityManager->persist($reponse);
             $entityManager->flush();



             return $this->redirectToRoute('app_reponse_index', [], Response::HTTP_SEE_OTHER);
         }

         // Rendre le modèle Twig avec le formulaire de réponse et la réclamation
         return $this->render('reclamation/show.html.twig', [
             'reclamation' => $reclamation,
             'reponse' => $reponse,
             'form' => $formReponse->createView(), // Utiliser createView() pour obtenir l'objet FormView
         ]);
     }



    #[Route('/{id}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
    }







}
