<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\Reponse;
use App\Form\ReclamationType;
use App\Form\ReponseType;
use App\Repository\ReclamationRepository;
use App\Repository\ReponseRepository;
use App\Repository\UserRepository;
use App\Service\TwilioService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Util\ClassUtils;


#[Route('/reclamation')]
class ReclamationController extends AbstractController
{
    #[Route('/', name: 'app_reclamation_index', methods: ['GET'])]
    public function index(ReclamationRepository $reclamationRepository, Request $request,PaginatorInterface $paginator): Response
    {

        $reclamationsCount = $reclamationRepository->countReclamations();
        $countNonTraiteReclamations = $reclamationRepository->countNonTraiteReclamations();
        $countTraiteReclamations = $reclamationRepository->countTraiteReclamations();
        $percentageNonTraite = number_format(($countNonTraiteReclamations / $reclamationsCount) * 100, 2);
        $percentageTraite = number_format(($countTraiteReclamations / $reclamationsCount) * 100, 2);


        $etat = $request->get('etat','all');
        $reclamations = [];

        if ($etat === 'Traité') {
            $reclamations = $reclamationRepository->findBy(['etat' => 'Traité']);
        } elseif ($etat === 'NonTraité') {
            $reclamations = $reclamationRepository->findBy(['etat' => 'NonTraité']);
        } elseif ($etat === 'all') {
            $reclamations = $reclamationRepository->findAll();
        }
     $count=count($reclamations);

        $reclamations= $paginator->paginate(
            $reclamations,
            $request->query->getInt('page', 1), // page number
            6 // limit per page
        );


        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamations,
            'etat' => $etat,
            'count'=>$count,
            'percentageNonTraite' => $percentageNonTraite,
            'percentageTraite' => $percentageTraite,

        ]);
    }


    #[Route('/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager ,UserRepository $utilisateurRepository,TwilioService $twilioService): Response
    {
        $reclamation = new Reclamation();
        $user=$utilisateurRepository->find(1);
        $reclamation->setUtilisateur($user);
        $reclamation->setNumTele(7777777);
        $reclamation->setEmail('belhouchet.koussay@esprit.tn');

        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reclamation);
            $entityManager->flush();
            $to="+21650889983";
            $body="Une nouvelle reclamation a etait ajouter";
            $twilioService->sendSms($to,$body);
            $this->addFlash('success', 'Your reclamation has been added successfully.');


            return $this->redirectToRoute('app_reclamation_client', [], Response::HTTP_SEE_OTHER);
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

    #[Route('/search', name: 'reclamation_search')]
    public function search(Request $request, ReclamationRepository $reclamationRepository)
    {
        $searchTerm = $request->query->get('q');

        $reclamations = $reclamationRepository->findEntitiesByString($searchTerm);

        // Formatage des résultats pour le renvoi au format JSON
        $formattedReclamations = [];
        foreach ($reclamations as $reclamation) {
            $formattedReclamations[] = [
                'nom' => $reclamation->getNom(),
                'prenom' => $reclamation->getPrenom(),
                'etat' => $reclamation->getEtat(),
                'email' => $reclamation->getEmail(),
                'sujet' => $reclamation->getSujet(),
                'id' => $reclamation->getId(), // Ajoutez d'autres champs si nécessaire
            ];
        }

        return new JsonResponse(['reclamations' => $formattedReclamations]);
    }
}
