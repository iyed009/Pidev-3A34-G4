<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Product;
use App\Form\Product1Type;
use App\Form\SearchForm;
use App\Repository\CategoriePRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
 
#[Route('/front/product')]
class PorductFrontController extends AbstractController
{
    #[Route('/', name: 'app_porduct_front_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository , CategoriePRepository $categoriePRepository, Request $request): Response
    {
        $data = new SearchData();
        $data->page = $request->get('page', 1);
        $form = $this -> createForm(SearchForm::class, $data);
        $form ->handleRequest($request);
        $products= $productRepository->findSearch($data);
        $categories = $categoriePRepository->findAll();

        return $this->render('porduct_front/index.html.twig', [
            'product' => $products,
            'categories' => $categories,
            'form' => $form ->createView()
        ]);
    }

    #[Route('/new', name: 'app_porduct_front_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $form = $this->createForm(Product1Type::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('app_porduct_front_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('porduct_front/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_porduct_front_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('porduct_front/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_porduct_front_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Product1Type::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_porduct_front_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('porduct_front/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_porduct_front_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_porduct_front_index', [], Response::HTTP_SEE_OTHER);
    }
}
