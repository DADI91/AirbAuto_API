<?php

namespace App\Controller;

use App\Services\FirebaseService;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Kreait\Firebase\Storage;

class ReportingController extends AbstractController
{
    private $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }


    /**
     * @Route("reporting/{userId}/{publicationId}", methods={"POST"})
     */
    public function createReporting(Request $request, $userId, $publicationId)
    {
        // Récupérer les données du document à partir de la requête
        $data = json_decode($request->getContent(), true);

        // Récupérer les références aux services Firestore et Storage
        $firestore = $this->firebaseService->getFirestore();

        // Récupérer la référence à la collection "documents" de Firestore

        $userReference = $firestore->collection("Reporting");

        $documentData = [
            "message_reporting" => $data["message_reporting"],
            "statut_reporting" => "En cours de traitement ",
            "id_Publication" => $publicationId,
            "id_user_reporting" => $userId,
        ];

        $newDocument = $userReference->add($documentData);

        // Récupérer la référence à la publication signalée
        $publicationReference = $firestore->collection("Publication")->document($publicationId);

        // Récupérer les données actuelles de la publication
        $publicationData = $publicationReference->snapshot()->data();

        // Incrémenter le nombre de signalements de la publication
        $newReportingCount = ($publicationData['reporting'] ?? 0) + 1;

        // Mettre à jour le nombre de signalements de la publication
        $publicationReference->update([
            ['path' => 'reporting', 'value' => $newReportingCount]
        ]);
       
        return new JsonResponse([
            "message" => "reporting éffecuté avec succès pour la publication", 
            "idPublication" => $publicationId,
            "userId" => $userId, 
            "documentId" => $newDocument->id(), 
            "documentData" => $documentData
        ]);
    }

    /**
     * @Route("reportings/publications/{publicationId}", methods={"GET"})
     */
    public function getReportingsByPublicationId($publicationId)
    {
        // Récupérer les références aux services Firestore
        $firestore = $this->firebaseService->getFirestore();

        // Récupérer la référence à la collection "Reporting" de Firestore
        $reportingsReference = $firestore->collection("Reporting");

        // Récupérer les documents qui ont l'id_Publication spécifié
        $query = $reportingsReference->where('id_Publication', '==', $publicationId);
        $documents = $query->documents();

        // Créer un tableau pour stocker les données des reportings
        $reportingsData = [];

        // Parcourir les documents et ajouter leurs données au tableau
        foreach ($documents as $document) {
            if ($document->exists()) {
                $reportingsData[] = $document->data();
            }
        }

        // Retourner un objet JSON contenant les données des reportings
        return new JsonResponse($reportingsData);
    }





    /**
     * @Route("publications/reporting", methods={"GET"})
     */
    public function getReportedPublications()
    {
        // Récupérer les références aux services Firestore
        $firestore = $this->firebaseService->getFirestore();

        // Récupérer la référence à la collection "Publication" de Firestore
        $publicationsReference = $firestore->collection("Publication");

        // Récupérer les documents dont le champ "reporting" est supérieur à 0
        $query = $publicationsReference->where('reporting', '>', 0);
        $documents = $query->documents();

        // Créer un tableau pour stocker les données des publications
        $publicationsData = [];

        // Parcourir les documents et ajouter leurs données au tableau
        foreach ($documents as $document) {
            if ($document->exists()) {
                $publicationsData[] = $document->data();
            }
        }

        // Retourner un objet JSON contenant les données des publications
        return new JsonResponse([
            "message" => "Toutes publications signalé récupérées avec succès ",
            "publications" => $publicationsData
            ]);
    }

    /**
     * @Route("reporting/{reportingId}", methods={"PUT"})
     */
    public function updateReporting(Request $request, $reportingId)
    {
        // Récupérer les données du document à partir de la requête
        $data = json_decode($request->getContent(), true);

        // Récupérer les références aux services Firestore
        $firestore = $this->firebaseService->getFirestore();

        // Récupérer la référence au document "Reporting" de Firestore
        $reportingReference = $firestore->collection('Reporting')->document($reportingId);

        // Créer un tableau pour stocker les mises à jour à effectuer
        $updates = [];

        // Mettre à jour les champs du reporting si nécessaire
        $fields = [
            "message_reporting",
            "statut_reporting",

        ];

        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $updates[] = ["path" => $field, "value" => $data[$field]];
            }
        }

        // Effectuer les mises à jour
        $reportingReference->update($updates);

        // Retourner un objet JSON contenant un message de succès
        return new JsonResponse(["message" => "Reporting modifié avec succès", "reportingId" => $reportingId]);
    }



    /**
     * @Route("reporting/{reportingId}", methods={"DELETE"})
     */
    public function deleteReporting($reportingId)
    {
        // Récupérer les références aux services Firestore
        $firestore = $this->firebaseService->getFirestore();

        // Récupérer la référence au document "Reporting" de Firestore
        $reportingReference = $firestore->collection('Reporting')->document($reportingId);

        // Supprimer le document
        $reportingReference->delete();

        // Retourner un objet JSON contenant un message de succès
        return new JsonResponse(["message" => "Reporting supprimé avec succès", "reportingId" => $reportingId]);
    }



 


    
}