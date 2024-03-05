<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Service\TwilioService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use OpenAI\Client;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class OpenIAController extends AbstractController
{
    #[Route("/transcribe", name: "transcribe", methods:["GET", "POST"])]
    public function transcribe(Request $request, EntityManagerInterface $entityManager,TwilioService $twilioService): Response
    {
        // Instantiate a new Reclamation entity
        $reclamation = new Reclamation();

        $to="+21650889983";
        $body="Une nouvelle reclamation a etait ajouter";
        $twilioService->sendSms($to,$body);
        $this->addFlash('success', 'Your reclamation has been added successfully.');

        if ($request->isMethod('POST')) {
            // Your transcription logic goes here
            // Make sure to set the $transcription variable
            $audioFile = $request->files->get('audioFile');

            // Ensure a file was uploaded
            if ($audioFile) {
                // Handle the uploaded file
                $audioFilePath = $this->getParameter('kernel.project_dir') . '/var/uploads/' . $audioFile->getClientOriginalName();
                $audioFile->move($this->getParameter('kernel.project_dir') . '/var/uploads/', $audioFile->getClientOriginalName());

                try {
                    $yourApiKey = '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'; // Use your own way to fetch the API key

                    $client = \OpenAI::client($yourApiKey);

                    // Transcribe audio
                    $response = $client->audio()->transcribe([
                        'model' => 'whisper-1',
                        'file' => fopen($audioFilePath, 'r'),
                        'response_format' => 'verbose_json',
                        'audio_encoding' => 'mp3', // Replace with the correct format
                    ]);

                    // Concatenate transcriptions from segments
                    $transcription = '';
                    foreach ($response->segments as $segment) {
                        $transcription .= $segment->text;
                    }
                    $user=$this->getUser();
                    $reclamation->setUtilisateur($user);
                    // Set Reclamation entity properties
                    $reclamation->setNom($user->getNom());
                    $reclamation->setPrenom($user->getPrenom());
                    $reclamation->setSujet('voice');
                    $reclamation->setNumTele($user->getNumTele());
                    $reclamation->setEmail($user->getEmail());

                    // Persist Reclamation entity to database
                    $entityManager->persist($reclamation);
                    $entityManager->flush();
                    $form = $this->createForm(ReclamationType::class, $reclamation);
                    $form->handleRequest($request);
                    // Render the template with the transcription
                    return $this->render('reclamation/voice.html.twig', [
                        'controller_name' => 'OpenIAController',
                        'transcription' => $transcription,
                        'form' => $form->createView(),
                    ]);
                } catch (\Exception $e) {
                    // Handle exceptions, e.g., log or show an error message
                    return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            }
        }

        // Render the template without transcription if no POST request or no file uploaded
        return $this->render('reclamation/voice.html.twig', [
            'controller_name' => 'OpenIAController',

        ]);
    }
}
