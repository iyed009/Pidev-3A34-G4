<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use app\Services\MailService;
use Dompdf\Options;
use Twilio\Rest\Client;

/**
 * @Route("/cart",name="cart_")
 */
class CartController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(SessionInterface $session, ProductRepository $productRepository)
    {
        $panier = $session->get("panier", []);

        // On "fabrique" les données
        $dataPanier = [];
        $total = 0;

        foreach($panier as $id => $quantite){
            $product = $productRepository->find($id);
            $dataPanier[] = [
                "product" => $product,
                "quantite" => $quantite
            ];
            $total += $product->getPrice() * $quantite;
        }

        return $this->render('cart/index.html.twig', [
            'dataPanier' => $dataPanier,
            'total' => $total,
        ]);
        }
    /**
     * @Route("/add/{id}",name="add")
     */
    public function add(Product $product, SessionInterface $session)
    {
            $panier =$session->get("panier",[]);
        $id= $product->getId();
            if(!empty($panier[$id])){
                $panier[$id]++;

            }else{
                $panier[$id] = 1;
            }
            $session->set("panier", $panier);
            return $this->redirectToRoute("cart_index");
    }

    /**
     * @Route("/mail/mail", name="email_add")
     */
    public function contactUser(MailerInterface $mailer, Request $request, SessionInterface $session, ProductRepository $productRepository): Response
    {
        // Récupérer les données du panier et le total
        $data = $this->getDataPanier($session, $productRepository);

        // Construire le sujet de l'e-mail en incluant les données du panier
        $subject = 'Reclamation Artist - Détails du panier';
        foreach ($data['dataPanier'] as $item) {
            $subject .= sprintf(" %s (quantité: %d),", $item['product']->getName(), $item['quantite']);
        }
        $subject = rtrim($subject, ',');

        // Construire l'e-mail avec le sujet dynamique
        $email = (new Email())
            ->from('daasala58@gmail.com')
            ->to('elfidha.ons@esprit.tn')
            ->subject($subject)
            ->text('Votre demande sera prise en compte et nous vous répondrons dans les meilleurs délais. Vous serez notifiés via une mail les détails de traitement de votre réclamation. Merci !!');

        // Envoyer l'e-mail
        $mailer->send($email);

        // Redirection vers la page du panier
        return $this->redirectToRoute('cart_index', [], Response::HTTP_SEE_OTHER);
    }
    /**
     * @Route("/cart/send-whatsapp-message", name="send_whatsapp_message")
     */
    public function sendWhatsAppMessage(): Response
    {
        $sid = 'AC790f372b99d9356c74aedab145d4268d';
        $token = '3d0ba83a7073558e4efc292b29fcd847';
        $twilioNumber = '+14155238886';

        $recipientNumber = '+21625446211'; // Le numéro de téléphone du destinataire

        $client = new Client($sid, $token);

        $message = $client->messages->create(
            "whatsapp:".$recipientNumber, // Numéro de téléphone du destinataire (format WhatsApp)
            [
                'from' => "whatsapp:".$twilioNumber, // Numéro Twilio autorisé à envoyer des messages WhatsApp
                'body' => 'Commande creted' // Votre message WhatsApp
            ]
        );

        return new Response('Message WhatsApp envoyé !');
    }
    /**
     * @Route("/remove/{id}", name="remove")
     */
    public function remove(Product $product, SessionInterface $session)
    {
        // On récupère le panier actuel
        $panier = $session->get("panier", []);
        $id = $product->getId();

        if(!empty($panier[$id])){
            if($panier[$id] > 1){
                $panier[$id]--;
            }else{
                unset($panier[$id]);
            }
        }

        // On sauvegarde dans la session
        $session->set("panier", $panier);


        return $this->redirectToRoute("cart_index");
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Product $product, SessionInterface $session)
    {
        // On récupère le panier actuel
        $panier = $session->get("panier", []);
        $id = $product->getId();

        if(!empty($panier[$id])){
            unset($panier[$id]);
        }

        // On sauvegarde dans la session
        $session->set("panier", $panier);

        return $this->redirectToRoute("cart_index");
    }
    /**
     * @Route("/download-pdf", name="download_pdf")
     */
    public function downloadPdf(SessionInterface $session, ProductRepository $productRepository): Response
    {
        // On récupère les données du panier et le total à partir de la méthode getDataPanier
        $data = $this->getDataPanier($session, $productRepository);

        // On charge le template Twig
        $html = $this->renderView('cart/pdf.html.twig', $data);

        // Configuration de Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');

        // Création de l'objet Dompdf
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);

        // Rendu du PDF
        $dompdf->render();

        // Téléchargement du PDF
        $dompdf->stream('panier.pdf', [
            'Attachment' => true,
        ]);
        //*whatsapp

        // Retourne une réponse vide
        return new Response();
    }
    /**
     * @Route("/delete", name="delete_all")
     */
    public function deleteAll(SessionInterface $session)
    {
        $session->remove("panier");

        return $this->redirectToRoute("cart_index");
    }
    private function getDataPanier(SessionInterface $session, ProductRepository $productRepository): array
    {
        // Initialise les données du panier et le total
        $dataPanier = [];
        $total = 0.0;

        // Logique pour récupérer les données du panier et calculer le total
        // Par exemple :
        $panier = $session->get("panier", []);

        foreach ($panier as $id => $quantite) {
            $product = $productRepository->find($id);
            if ($product) {
                $dataPanier[] = [
                    "product" => $product,
                    "quantite" => $quantite
                ];
                $total += $product->getPrice() * $quantite;
            }
        }

        // Retourne les données du panier et le total
        return [
            'dataPanier' => $dataPanier,
            'total' => $total
        ];
    }


}
