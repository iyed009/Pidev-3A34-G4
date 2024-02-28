<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\Ticket;
use App\Form\Ticket1Type;
use App\Repository\TicketRepository;
use App\Services\QrcodeService;
use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\QrCode;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/front/ticket')]
class TicketFrontController extends AbstractController
{


    #[Route('/', name: 'app_ticket_front_index', methods: ['GET'])]
    public function index(TicketRepository $ticketRepository): Response
    {
        return $this->render('ticket_front/index.html.twig', [
            'tickets' => $ticketRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_ticket_front_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ticket = new Ticket();
        $form = $this->createForm(Ticket1Type::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ticket);
            $entityManager->flush();

            return $this->redirectToRoute('app_ticket_front_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ticket_front/new.html.twig', [
            'ticket' => $ticket,
            'form' => $form,
        ]);
    }



    #[Route('/{id}/edit', name: 'app_ticket_front_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ticket $ticket, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Ticket1Type::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_ticket_front_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ticket_front/edit.html.twig', [
            'ticket' => $ticket,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ticket_front_delete', methods: ['POST'])]
    public function delete(Request $request, Ticket $ticket, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ticket->getId(), $request->request->get('_token'))) {
            $entityManager->remove($ticket);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_ticket_front_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_ticket_front_show', methods: ['GET'])]
    public function show(Ticket $ticket): Response
    {

        return $this->render('ticket_front/show.html.twig', [
            'ticket' => $ticket,
        ]);
    }
    #[Route('/decrement-ticket/{id}', name: 'decrement_ticket')]

    public function decrementTicket(Ticket $ticket, TicketRepository $ticketRepository , EntityManagerInterface $entityManager): Response
    {
        try {
            $ticketRepository->decrementTicket($ticket);
            $entityManager->flush();



            $content = '<p>Vous avez participer à l Evenement " ' .  $ticket->getEvenement()->getNom(). '" </p>';
            $content .= '<p>Aura lieu au : ' .  $ticket->getEvenement()->getLieu(). '</p>';
            $content .= '<p>Date : ' . $ticket->getEvenement()->getDateEvenement()->format('d M Y | H:i') . '</p>';
            $content .= '<p>Ticket Type: ' . $ticket->getType() . '</p>';
            $content .= '<p>Ticket Prix: ' . $ticket->getPrix() . '</p>';

            $subject='Paricipation!';
            $transport = Transport::fromDsn('gmail+smtp://belhouchet.koussay@esprit.tn:mqbzmifqiyinzfux@smtp.gmail.com:587');

            $mailerWithTransport = new Mailer($transport);
            $email = (new Email())
                ->from('belhouchet.koussay@esprit.tn')
                ->to('majed.smichi@esprit.tn')
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject($subject)
                // ->text('Sending emails is fun again!')
                ->html($content);


            $mailerWithTransport->send($email);

            return $this->render('ticket_front/_form.html.twig', [
                'ticket' => $ticket,
            ]);
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 400);
        }
    }

}
