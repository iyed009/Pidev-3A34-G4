<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/evenement')]
class EvenementController extends AbstractController
{
    #[Route('/', name: 'app_evenement_index', methods: ['GET', 'POST'])]
    public function index(PaginatorInterface $paginator ,Request $request,EvenementRepository $evenementRepository, EntityManagerInterface $entityManager): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image_evenement')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $evenement->setImageEvenement($newFilename);
                $entityManager->persist($evenement);
                $entityManager->flush();

                return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
            }


        }
        $evenements = $evenementRepository->findAll();
        $evenements= $paginator->paginate(
            $evenements,
            $request->query->getInt('page', 1), // page number
            3 // limit per page
        );
        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenements,
            'form' => $form->createView(),
            'errors' => $form->getErrors(true, false),

        ]);
    }








    #[Route('/new', name: 'app_evenement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($evenement);
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evenement/index.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evenement_show',  methods: ['GET', 'POST'])]
    public function show(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    { $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $imageFile = $form->get('image_evenement')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $evenement->setImageEvenement($newFilename);

                $entityManager->flush();


                return $this->redirectToRoute('app_evenement_show', ['id' => $evenement->getId()], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('evenement/show.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_evenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_evenement_delete', methods: ['POST'])]
    public function delete(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evenement->getId(), $request->request->get('_token'))) {
            $entityManager->remove($evenement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/tri/prix', name: 'produit_tri' )]
    public function tri(ProduitRepository $repo,SousCategorieRepository $sousCategorieRepository, PaginatorInterface $paginator,Request $request): Response
    {   $data =  $repo->findProductsByPrice();
        $produits = $paginator->paginate(
            $data, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            3 /*limit per page*/
        );


        return $this->render('produit/front.html.twig', [
            'sous_categories' => $sousCategorieRepository->findAll(),
            'produits' => $produits,
        ]);
    }

    #[Route('/tri/desc', name: 'produit_desc' )]
    public function tridesc(ProduitRepository $repo,SousCategorieRepository $sousCategorieRepository, PaginatorInterface $paginator,Request $request): Response
    {
        $data =  $repo->findProductsByPriceDESC();
        $produits = $paginator->paginate(
            $data, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            3 /*limit per page*/
        );

        return $this->render('produit/front.html.twig', [
            'sous_categories' => $sousCategorieRepository->findAll(),
            'produits' => $produits,
        ]);
    }

    #[Route('/tri/nom', name: 'produit_tri_nom' )]
    public function trinom(EvenementRepository $repo, PaginatorInterface $paginator,Request $request,EntityManagerInterface $entityManager): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image_evenement')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $evenement->setImageEvenement($newFilename);
                $entityManager->persist($evenement);
                $entityManager->flush();

                return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
            }
        }
        $data =  $repo->findProductsByName();
        $produits = $paginator->paginate(
            $data, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            3 /*limit per page*/
        );

        return $this->render('evenement/index.html.twig', [

            'form' => $form->createView(),
            'evenements' => $produits,

        ]);
    }
}