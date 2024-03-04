<?php

namespace App\Controller;

use App\Entity\CategorieP;
use App\Form\CategoriePType;
use App\Repository\CategoriePRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categoriep')]
class CategoriePController extends AbstractController
{
    #[Route('/', name: 'app_categoriep_index', methods: ['GET', 'POST'])]
    public function index(Request $request, CategoriePRepository $categoriePRepository, EntityManagerInterface $entityManager): Response
    {
        $categorieP = new CategorieP();
        $form = $this->createForm(CategoriePType::class, $categorieP);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categorieP);
            $entityManager->flush();

            return $this->redirectToRoute('app_categoriep_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('categorie_p/index.html.twig', [
            'categories' => $categoriePRepository->findAll(),
            'form' => $form->createView(),
        ]);
    }

    #[Route('/new', name: 'app_categoriep_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorieP = new CategorieP();
        $form = $this->createForm(CategoriePType::class, $categorieP);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categorieP);
            $entityManager->flush();

            return $this->redirectToRoute('app_categoriep_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('categorie_p/index.html.twig', [
            'categorieP' => $categorieP,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_categoriep_show', methods: ['GET'])]
    public function show(CategorieP $categorieP): Response
    {
        return $this->render('categorie_p/show.html.twig', [
            'categorieP' => $categorieP,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_categoriep_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CategorieP $categorieP, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoriePType::class, $categorieP);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_categoriep_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('categorie_p/edit.html.twig', [
            'categories' => $categorieP,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_categoriep_delete', methods: ['POST'])]
    public function delete(Request $request, CategorieP $categorieP, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorieP->getId(), $request->request->get('_token'))) {
            $entityManager->remove($categorieP);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_categoriep_index', [], Response::HTTP_SEE_OTHER);
    }
}
