<?php

namespace App\Controller;

use App\Entity\Activite;
use App\Form\Activite1Type;
use App\Repository\ActiviteRepository;
use App\Repository\UserRepository;
use App\Service\TwilioReservation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/front/activite')]
class FrontActiviteController extends AbstractController
{
    #[Route('/', name: 'app_front_activite_index', methods: ['GET'])]
    public function index(ActiviteRepository $activiteRepository): Response
    {

        return $this->render('front_activite/index.html.twig', [
            'activites' => $activiteRepository->findAll(),
        ]);
    }#[Route('/mesReservation', name: 'app_front_activite_reservation', methods: ['GET'])]
public function reservation(ActiviteRepository $activiteRepository): Response
{

    return $this->render('front_activite/mesReservations.html.twig', [
        'activites' => $activiteRepository->findActivitiesByUserId(1),
    ]);
}
    #[Route('/res/{id}', name: 'reserver', methods: ['GET','POST'])]
public function réserver(Activite $activite,ActiviteRepository $activiteRepository,UserRepository $userRepository,$id, EntityManagerInterface $entityManager,TwilioReservation $twilioReservation): Response
{
        $user = $userRepository->find(3);
        $act = $activiteRepository->find($id);
        $act->addReservation($user);
        $user->addActivite($act);
        $entityManager->persist($act);
        $entityManager->persist($user);
        $entityManager->flush();
        $to = "+21658076383";
        $body = "Une nouvelle réservation pour l'activitée : ". $activite->getNom() ." au sein du salle :  " .$activite->getSalle()->getNom()." est ajoutée, nombre totale du réservation" .$activite->getReservation()->count();
        $twilioReservation->sendSms($to, $body);

        $content = '<p>Votre réservation est confirmée !  ' . $activite->getNom() . '</p>';
        $content .= '<p>Bonjour,</p>';
        $content .= '<p>Nous sommes ravis de vous informer que votre réservation pour l activitée '.$activite->getNom().' dans la salle : ' . $activite->getSalle()->getNom() .' situé à '.$activite->getSalle()->getAddresse().' est condirmé </p>';
        $content .= '<p>Préparez-vous à passer un moment inoubliable et à créer des souvenirs mémorables avec le coach '.$activite->getCoach().'</p>';
        $content .= '<p>N oubliez pas d apporter votre énergie positive et votre esprit d équipe le ' . $activite->getDate()->format('d M Y à H:i') . '</p>';
        $content .= '<p>Si vous avez des questions ou besoin d assistance supplémentaire merci de nous contacter par téléphone : ' . $activite->getSalle()->getNumTel() .' ou via E-mail du responsbale : '.$activite->getSalle()->getUtilisateur()->getEmail().'</p>';


        $subject = 'Confirmation du réservation!';
        $transport = Transport::fromDsn('smtp://louay.saad@esprit.tn:svmwcgirfddipchm@smtp.gmail.com:587');

        $mailerWithTransport = new Mailer($transport);
        $email = (new Email())
            ->from('louay.saad@esprit.tn')
            ->to($user->getEmail())
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject($subject)
            // ->text('Sending emails is fun again!')
            ->html($content);
        $mailerWithTransport->send($email);

        return $this->redirectToRoute('app_front_activite_reservation', [], Response::HTTP_SEE_OTHER);


}

    #[Route('/new', name: 'app_front_activite_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $activite = new Activite();
        $form = $this->createForm(Activite1Type::class, $activite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($activite);
            $entityManager->flush();

            return $this->redirectToRoute('app_front_activite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('front_activite/new.html.twig', [
            'activite' => $activite,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_front_activite_show', methods: ['GET'])]
    public function show(Activite $activite): Response
    {
        return $this->render('front_activite/show.html.twig', [
            'activite' => $activite,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_front_activite_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Activite $activite, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Activite1Type::class, $activite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_front_activite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('front_activite/edit.html.twig', [
            'activite' => $activite,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_front_activite_delete', methods: ['POST'])]
    public function delete(Request $request, Activite $activite, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$activite->getId(), $request->request->get('_token'))) {
            $entityManager->remove($activite);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_front_activite_index', [], Response::HTTP_SEE_OTHER);
    }
}
