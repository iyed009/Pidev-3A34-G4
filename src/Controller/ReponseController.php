<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\Reponse;
use App\Form\ReponseType;
use App\Repository\ReponseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/reponse')]
class ReponseController extends AbstractController
{
    #[Route('/', name: 'app_reponse_index', methods: ['GET'])]
    public function index(ReponseRepository $reponseRepository): Response
    {
        return $this->render('reponse/index.html.twig', [
            'reponses' => $reponseRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_reponse_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'identifiant de la réclamation à partir de la requête
        $reclamationId = $request->query->get('id');
        // Récupérer l'objet Reclamation correspondant depuis la base de données
        $reclamation = $entityManager->getRepository(Reclamation::class)->find($reclamationId);
        $reponse = new Reponse();
        $reponse->setIdReclamation($reclamation); // Définir la réclamation pour la réponse



        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reponse);
            $entityManager->flush();

            $reclamation->setEtat("Traité");
            $entityManager->persist($reclamation);
            $entityManager->flush();


            return $this->redirectToRoute('app_reponse_index', [], Response::HTTP_SEE_OTHER);

        }


        return $this->renderForm('reponse/new.html.twig', [
            'reponse' => $reponse,
            'form' => $form,
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/{id}', name: 'app_reponse_show', methods: ['GET' ,'POST'])]
    public function show(Reponse $reponse, Request $request, EntityManagerInterface $entityManager, Reclamation $reclamation): Response
    {
        // Récupérez l'ID de la réponse
        $reclamation=$reponse->getIdReclamation();

        $reponseId = $reponse->getId();

        // Créez le formulaire en utilisant la classe ReponseType
        $form = $this->createForm(ReponseType::class, $reponse);

        // Traitez la requête HTTP
        $form->handleRequest($request);

        // Vérifiez si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrez les modifications dans la base de données
            $entityManager->flush();

            // Redirigez l'utilisateur vers une autre page en fournissant l'ID de la réponse
            return $this->redirectToRoute('app_reponse_show', ['id' => $reponseId], Response::HTTP_SEE_OTHER);
        }

        // Rendez le template Twig avec le formulaire
        return $this->render('reponse/show.html.twig', [
            'reponse' => $reponse,
            'form' => $form->createView(),
            'reclamation'=>$reclamation// Créez une vue du formulaire
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reponse_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reponse $reponse, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reponse_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reponse/edit.html.twig', [
            'reponse' => $reponse,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reponse_delete', methods: ['POST'])]
    public function delete(Request $request, Reponse $reponse, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reponse->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reponse);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reponse_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/remove/{id}', name: 'app_reclamation_delete2', methods: ['GET','POST'])]
    public function delete2($id, ReponseRepository $reclamationRepository , EntityManagerInterface $entityManager): Response
    {

        $rec = $reclamationRepository->find($id);
        $entityManager->remove($rec);
        $entityManager->flush();


        return $this->redirectToRoute('app_reponse_index', [], Response::HTTP_SEE_OTHER);
    }
}
