<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\Reponse;
use App\Form\ReponseType;
use App\Repository\ReclamationRepository;
use App\Repository\ReponseRepository;
use App\Service\TwilioService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/reponse')]
class ReponseController extends AbstractController
{
    #[Route('/', name: 'app_reponse_index', methods: ['GET'])]
    public function index(Request $request, ReponseRepository $reponseRepository, PaginatorInterface $paginator): Response
    {
        $reponses = $reponseRepository->findAll();
        $count=count($reponses);


        $reponses = $paginator->paginate(
            $reponses,
            $request->query->getInt('page', 1), // page number
            4 // limit per page
        );

        return $this->render('reponse/index.html.twig', [
            'reponses' => $reponses,
            'count'=>$count,
        ]);
    }


    #[Route('/new', name: 'app_reponse_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager ): Response
    {
        $reclamationId = $request->query->get('id');
        $reclamation = $entityManager->getRepository(Reclamation::class)->find($reclamationId);
        $reponse = new Reponse();
        $reponse->setIdReclamation($reclamation);



        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reponse);
            $entityManager->flush();


            $reclamation->setEtat("Traité");
            $entityManager->persist($reclamation);
            $entityManager->flush();
            $to=$reclamation->getEmail();
            $content = '<p>See Twig integration for better HTML integration!</p>';
            $subject='It work!';
            $transport = Transport::fromDsn('gmail+smtp://iyed.ouederni@esprit.tn:jzagybphgctjripq@smtp.gmail.com:587');

            // Create a Mailer instance with the specified transport
            $mailerWithTransport = new Mailer($transport);
            $email = (new Email())
                ->from('iyed.ouederni@esprit.tn')
                ->to($to)
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject($subject)
                // ->text('Sending emails is fun again!')
                ->html($content);


            $mailerWithTransport->send($email);


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
        $reclamation=$reponse->getIdReclamation();
        $reponseId = $reponse->getId();
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_reponse_show', ['id' => $reponseId], Response::HTTP_SEE_OTHER);
        }
        return $this->render('reponse/show.html.twig', [
            'reponse' => $reponse,
            'form' => $form->createView(),
            'reclamation'=>$reclamation
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
    #[Route('/search1', name: 'reponse_search')]
    public function search1(Request $request, ReponseRepository $reponseRepository)
    {
        $searchTerm = $request->query->get('q');

        $reponses = $reponseRepository->findEntitiesByString($searchTerm);

        // Formatage des résultats pour le renvoi au format JSON
        $formattedReponses = [];
        foreach ($reponses as $reponse) {
            $formattedReponses[] = [
                'reponse' => $reponse->getReponse(),
                'date' => $reponse->getDate()->format('Y-m-d H:i:s'), // Assurez-vous que getDate() renvoie un objet DateTime
                'id' => $reponse->getId(), // Ajoutez d'autres champs si nécessaire
            ];
        }

        return new JsonResponse(['reponses' => $formattedReponses]);
    }

}
