<?php

namespace App\Controller;

use App\Entity\Salle;
use App\Form\SalleType;
use App\Repository\ActiviteRepository;
use App\Repository\ReclamationRepository;
use App\Repository\SalleRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/salle')]
class SalleController extends AbstractController
{
    #[Route('/', name: 'app_salle_index', methods: ['GET','POST'])]
    public function index(SalleRepository $salleRepository,Request $request,EntityManagerInterface $entityManager): Response
    {
        $salle = new Salle();
        $form = $this->createForm(SalleType::class, $salle);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('logoSalle')->getData();
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
                $salle->setLogoSalle($newFilename);

                $entityManager->persist($salle);
                $entityManager->flush();

                return $this->redirectToRoute('app_salle_index', [], Response::HTTP_SEE_OTHER);
            }
        }

            return $this->render('salle/index.html.twig', [
                'salles' => $salleRepository->findAll(),
                'form' => $form->createView(),
                'errors' => $form->getErrors(true, false),
            ]);
        }


    #[Route('/new', name: 'app_salle_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $salle = new Salle();
        $form = $this->createForm(SalleType::class, $salle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($salle);
            $entityManager->flush();

            return $this->redirectToRoute('app_salle_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('salle/new.html.twig', [
            'salle' => $salle,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_salle_show', methods: ['GET','POST'])]
    public function show(Salle $salle,ActiviteRepository $activiteRepository ,Request $request,EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SalleType::class, $salle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $imageFile = $form->get('logoSalle')->getData();
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
                $salle->setLogoSalle($newFilename);

                $entityManager->flush();


                return $this->redirectToRoute('app_salle_show', ['id'=>$salle->getId()], Response::HTTP_SEE_OTHER);
            }
        }


        return $this->render('salle/show.html.twig', [
            'salle' => $salle,
            'form' => $form->createView(),
            'errors' => $form->getErrors(true, false),
            'activites' => $activiteRepository->findBySalle($salle)
        ]);
    }

    #[Route('/{id}/edit', name: 'app_salle_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Salle $salle, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SalleType::class, $salle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_salle_show', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('salle/edit.html.twig', [
            'salle' => $salle,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_salle_delete', methods: ['POST','DELETE'])]
    public function delete(Request $request, Salle $salle, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$salle->getId(), $request->request->get('_token'))) {
            $entityManager->remove($salle);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_salle_index', [], Response::HTTP_SEE_OTHER);
    }

}
