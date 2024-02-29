<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use App\Services\TwilioEvenement;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/evenement')]
class EvenementController extends AbstractController
{
    #[Route('/', name: 'app_evenement_index', methods: ['GET', 'POST'])]
    public function index(TwilioEvenement $twilioEvenement,PaginatorInterface $paginator ,Request $request,EvenementRepository $evenementRepository, EntityManagerInterface $entityManager): Response
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
                $to="+21628913441";
                $body="Une nouvelle evenement a etait ajouter";
                $twilioEvenement->sendSms($to,$body);
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

    #[Route('/tri/nom', name: 'evenement_tri_nom' )]
    public function trinom(EvenementRepository $repo, PaginatorInterface $paginator,Request $request,EntityManagerInterface $entityManager): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        $data =  $repo->findProductsByName();
        $evenements = $paginator->paginate(
            $data, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            3 /*limit per page*/
        );

        return $this->render('evenement/index.html.twig', [

            'form' => $form->createView(),
            'evenements' => $evenements,

        ]);
    }

    #[Route('/search1', name: 'evenement_search1')]
    public function search1(Request $request, EvenementRepository $evenementRepository)
    {
        $searchTerm = $request->query->get('q');

        $evenements = $evenementRepository->findEntitiesByString1($searchTerm);

        // Formatage des résultats pour le renvoi au format JSON
        $formattedEvenements = [];
        foreach ($evenements as $evenement) {
            $formattedEvenements[] = [
                'nom' => $evenement->getNom(),
                'date' => $evenement->getDateEvenement() ? $evenement->getDateEvenement()->format('Y-m-d ') : null,// Utiliser getDateEvenement au lieu de setDateEvenement
                'heure' => $evenement->getDateEvenement() ? $evenement->getDateEvenement()->format('H:i ') : null,// Utiliser getDateEvenement au lieu de setDateEvenement
                'lieu' => $evenement->getLieu(),
                'description' => $evenement->getDescription(),
                'imageEvenement' => $evenement->getImageEvenement(),
                'id' => $evenement->getId(),
                // Add other fields as needed
            ];
        }

        return new JsonResponse(['evenements' => $formattedEvenements]);
    }


}
