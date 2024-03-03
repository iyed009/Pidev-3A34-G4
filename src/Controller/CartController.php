<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;

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
