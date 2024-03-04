<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Form\TicketType;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ticket')]
class TicketController extends AbstractController
{
    #[Route('/', name: 'app_ticket_index', methods: ['GET', 'POST'])]
    public function index( PaginatorInterface $paginator,Request $request, TicketRepository $ticketRepository, EntityManagerInterface $entityManager): Response
    {
        $ticket = new Ticket();
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entityManager->persist($ticket);
                $entityManager->flush();

                return $this->redirectToRoute('app_ticket_index', [], Response::HTTP_SEE_OTHER);
            }
        }
        $tickets = $ticketRepository->findAll();
        $tickets= $paginator->paginate(
            $tickets,
            $request->query->getInt('page', 1), // page number
            3 // limit per page
        );


        return $this->render('ticket/index.html.twig', [
            'tickets' => $tickets,
            'form' => $form->createView(),
            'errors' => $form->getErrors(true, false), // Pass errors to Twig (empty array if no errors)
        ]);
    }


    #[Route('/new', name: 'app_ticket_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ticket = new Ticket();
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ticket);
            $entityManager->flush();

            return $this->redirectToRoute('app_ticket_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ticket/new.html.twig', [
            'ticket' => $ticket,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ticket_show', methods: ['GET', 'POST'])]
    public function show(Request $request,Ticket $ticket, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_ticket_show', ['id' => $ticket->getId()], Response::HTTP_SEE_OTHER);

        }

        // Fetch the users associated with this ticket
        $users = $ticket->getUsers();
        return $this->render('ticket/show.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
            'users' => $users,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ticket_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ticket $ticket, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_ticket_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ticket/edit.html.twig', [
            'ticket' => $ticket,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_ticket_delete', methods: ['POST'])]
    public function delete(Request $request, Ticket $ticket, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ticket->getId(), $request->request->get('_token'))) {
            $entityManager->remove($ticket);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_ticket_index', [], Response::HTTP_SEE_OTHER);
    }



    #[Route('/search', name: 'ticket_search')]
    public function search(Request $request, TicketRepository $ticketRepository)
    {
        $searchTerm = $request->query->get('q');

        $tickets = $ticketRepository->findEntitiesByString($searchTerm);

        // Formatage des résultats pour le renvoi au format JSON
        $formattedTickets = []; // <----- Initialisez le tableau ici
        foreach ($tickets as $ticket) {
            $formattedTickets[] = [
                'prix' => $ticket->getPrix(),
                'type' => $ticket->getType(),
                'nbreTicket' => $ticket->getNbreTicket(),
                'evenement' => $ticket->getEvenement()->getNom(),


                'id' => $ticket->getId(), // Ajoutez d'autres champs si nécessaire
            ];
        }

        return new JsonResponse(['tickets' => $formattedTickets]);

    }




    #[Route('/tri/asc', name: 'ticket_tri_asc' )]
    public function tri(TicketRepository $repo, PaginatorInterface $paginator,Request $request): Response
    {
        $ticket = new Ticket();

        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        $data =  $repo->findTicketsByPrice();
        $tickets = $paginator->paginate(
            $data, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            3 /*limit per page*/
        );


        return $this->render('ticket/index.html.twig', [
            'tickets' => $tickets,
            'form' => $form->createView(),

        ]);
    }

    #[Route('/tri/desc', name: 'ticket_tri_desc' )]
    public function tridesc(TicketRepository $repo, PaginatorInterface $paginator,Request $request): Response
    {

        $ticket = new Ticket();

        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        $data =  $repo->findTicketsByPriceDESC();
        $tickets = $paginator->paginate(
            $data, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            3 /*limit per page*/
        );

        return $this->render('ticket/index.html.twig', [
            'form' => $form->createView(),

            'tickets' => $tickets,
        ]);
    }





}
