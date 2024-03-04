<?php

namespace App\Controller;

use App\Entity\Activite;
use App\Form\ActiviteType;
use App\Repository\ActiviteRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use phpDocumentor\Reflection\Types\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/activite')]
class ActiviteController extends AbstractController
{
    #[Route('/', name: 'app_activite_index', methods: ['GET', 'POST'])]
    public function index(PaginatorInterface $paginator,ActiviteRepository $activiteRepository, Request $request,EntityManagerInterface $entityManager): Response
    {
        $activite = new Activite();
        $form = $this->createForm(ActiviteType::class, $activite);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageActivte')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory_activite'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $activite->setImageActivte($newFilename);

                $entityManager->persist($activite);
                $entityManager->flush();

                return $this->redirectToRoute('app_activite_index', [], Response::HTTP_SEE_OTHER);
            }
        }
        $activites = $activiteRepository->findAll();


        $activites= $paginator->paginate(
            $activites,
            $request->query->getInt('page', 1), // page number
            3 // limit per page
        );
        return $this->render('activite/index.html.twig', [
            'activites' => $activites,
            'form' => $form->createView(),
            'errors' => $form->getErrors(true, false),
        ]);
    }

  /*  #[Route('/new', name: 'app_activite_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $activite = new Activite();
        $form = $this->createForm(ActiviteType::class, $activite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($activite);
            $entityManager->flush();

            return $this->redirectToRoute('app_activite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('activite/new.html.twig', [
            'activite' => $activite,
            'form' => $form,
        ]);
    }
*/



    #[Route('/{id}', name: 'app_activite_show', methods: ['GET', 'POST'])]
    public function show(Activite $activite,Request $request,EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(ActiviteType::class, $activite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $imageFile = $form->get('imageActivte')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory_activite'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $activite->setImageActivte($newFilename);

                $entityManager->flush();

                return $this->redirectToRoute('app_activite_show', ['id'=>$activite->getId()], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('activite/show.html.twig', [
            'activite' => $activite,
            'form' => $form->createView(),
            'errors' => $form->getErrors(true, false),

        ]);
    }

  /*  #[Route('/{id}/edit', name: 'app_activite_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Activite $activite, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ActiviteType::class, $activite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_activite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('activite/edit.html.twig', [
            'activite' => $activite,
            'form' => $form,
        ]);
    }*/

    #[Route('/{id}/delete', name: 'app_activite_delete', methods: ['POST'])]
    public function delete(Request $request, Activite $activite, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$activite->getId(), $request->request->get('_token'))) {
            $entityManager->remove($activite);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_activite_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/tri/nbrmax', name: 'activite_tri' )]
    public function tri(ActiviteRepository $repo, PaginatorInterface $paginator,Request $request,EntityManagerInterface $entityManager): Response
    {

        $activite = new Activite();
        $form = $this->createForm(ActiviteType::class, $activite);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageActivte')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory_activite'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $activite->setImageActivte($newFilename);

                $entityManager->persist($activite);
                $entityManager->flush();

                return $this->redirectToRoute('app_activite_index', [], Response::HTTP_SEE_OTHER);
            }
        }
        $data =  $repo->findActiviteByNbrAbonnes();
        $activites = $paginator->paginate(
            $data, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            3 /*limit per page*/
        );
        return $this->render('activite/index.html.twig', [

            'form' => $form->createView(),
            'activites' =>  $activites,
            'errors' => $form->getErrors(true, false),
        ]);
    }

    #[Route('/tri/desc', name: 'activite_desc' )]
    public function tridesc(ActiviteRepository $repo, PaginatorInterface $paginator,Request $request,EntityManagerInterface $entityManager): Response
    {
        $activite = new Activite();
        $form = $this->createForm(ActiviteType::class, $activite);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageActivte')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory_activite'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $activite->setImageActivte($newFilename);

                $entityManager->persist($activite);
                $entityManager->flush();

                return $this->redirectToRoute('app_activite_index', [], Response::HTTP_SEE_OTHER);
            }
        }
        $data =  $repo->findActiviteByNbrAbonnesDESC();
        $activites = $paginator->paginate(
            $data, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            3 /*limit per page*/
        );
        return $this->render('activite/index.html.twig', [

            'form' => $form->createView(),
            'activites' =>  $activites,
            'errors' => $form->getErrors(true, false),
        ]);
    }

    #[Route('/tri/nom', name: 'activite_tri_nom' )]
    public function trinom(ActiviteRepository $repo, PaginatorInterface $paginator,Request $request,EntityManagerInterface $entityManager): Response
    {
        $activite = new Activite();
        $form = $this->createForm(ActiviteType::class, $activite);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageActivte')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory_activite'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $activite->setImageActivte($newFilename);

                $entityManager->persist($activite);
                $entityManager->flush();

                return $this->redirectToRoute('app_activite_index', [], Response::HTTP_SEE_OTHER);
            }
        }
        $data =  $repo->findactiviteByName();
        $activites = $paginator->paginate(
            $data, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            3 /*limit per page*/
        );
        return $this->render('activite/index.html.twig', [

            'form' => $form->createView(),
            'activites' =>  $activites,
            'errors' => $form->getErrors(true, false),
        ]);
    }
    #[Route('/search2', name: 'activite_search2')]
    public function search2(Request $request, ActiviteRepository $activiteRepository)
    {
        $searchTerm = $request->query->get('q');

        $activites = $activiteRepository->findEntitiesByString($searchTerm);

        // Formatage des résultats pour le renvoi au format JSON
        $formattedActivites = [];
        foreach ($activites as $activite) {
            $formattedActivites[] = [
                'nom' => $activite->getNom(),
                'date' => $activite->getDate() ? $activite->getDate()->format('Y-m-d H:i') : null,
                'nbrMax' => $activite->getNbrMax(),
                'coach' => $activite->getCoach(),
                'description' => $activite->getDescription(),
                'imageActivte' => $activite->getImageActivte(), // Adjust this according to your property name
                'id' => $activite->getId(),
                // Add other fields as needed
            ];
        }

        return new JsonResponse(['activites' => $formattedActivites]);
    }

}
