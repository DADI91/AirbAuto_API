<?php

namespace App\Controller;

use App\Services\FirebaseService;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Kreait\Firebase\Storage;

class ReservationController extends AbstractController
{
    private $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }


    /**
     * @Route("reservation/{userId}/{publicationId}", methods={"POST"})
     */
    public function createReservation(Request $request, $userId, $publicationId)
    {
        // Récupérer les données du document à partir de la requête
        $data = json_decode($request->getContent(), true);

        // Récupérer les références aux services Firestore et Storage
        $firestore = $this->firebaseService->getFirestore();

        // Récupérer la référence à la collection "documents" de Firestore

        $userReference = $firestore->collection("Reservation");

        $documentData = [
            "message_reservation" => $data["message_reservation"],
            "date_debut" => $data["date_debut"],
            "date_fin" => $data["date_fin"],
            "statut_reservation" => "En cours",
            "id_Publication" => $publicationId,
            "id_user_reservation" => $userId,
        ];

        $newDocument = $userReference->add($documentData);

        $newDocument->update([['path' => 'ID_Reservation', 'value' => $newDocument->id()]]);

       
        return new JsonResponse([
            "message" => "Reservation éffecuté avec succès pour l'utilisateur", 
            "userId" => $userId, 
            "idPublication" => $publicationId,
            "documentId" => $newDocument->id(), 
            "documentData" => $documentData
        ]);
    }

    /**
     * @Route("reservation/publication/{publicationId}", methods={"GET"})
     */
    public function getReservationByPublicationId($publicationId)
    {
        // Récupérer les références aux services Firestore
        $firestore = $this->firebaseService->getFirestore();

        // Récupérer la référence à la collection "Reservation" de Firestore
        $reservationCollection = $firestore->collection("Reservation");

        // Créer une requête pour récupérer les réservations associées à la publication spécifique
        $query = $reservationCollection->where("id_Publication", "==", $publicationId);
        $reservationsSnapshot = $query->documents();

        // Tableau pour stocker les réservations récupérées
        $reservations = [];

        // Parcourir les documents récupérés
        foreach ($reservationsSnapshot as $reservationSnapshot) {
            $reservationData = $reservationSnapshot->data();
            $reservations[] = $reservationData;
        }

        return new JsonResponse([
            "message" => "Réservations récupérées avec succès pour la publication",
            "publicationId" => $publicationId,
            "reservations" => $reservations,
        ]);
    }

    /**
     * @Route("reservation/user/{userId}", methods={"GET"})
     */
    public function getReservationByUserId($userId)
    {
        // Récupérer les références aux services Firestore
        $firestore = $this->firebaseService->getFirestore();

        // Récupérer la référence à la collection "Reservation" de Firestore
        $reservationCollection = $firestore->collection("Reservation");

        // Créer une requête pour récupérer les réservations associées à la publication spécifique
        $query = $reservationCollection->where("id_user_reservation", "==", $userId);
        $reservationsSnapshot = $query->documents();

        // Tableau pour stocker les réservations récupérées
        $reservations = [];

        // Parcourir les documents récupérés
        foreach ($reservationsSnapshot as $reservationSnapshot) {
            $reservationData = $reservationSnapshot->data();
            $reservations[] = $reservationData;
        }

        return new JsonResponse([
            "message" => "Réservations récupérées avec succès pour la l'utilisateur qui a reserver",
            "userId" => $userId,
            "reservations" => $reservations,
        ]);
    }




    /**
     * @Route("reservation/{userId}/{reservationId}", methods={"PUT"})
     */
    public function updateReservation(Request $request, $userId, $reservationId)
    {
        // Récupérer les données de la réservation à partir de la requête
        $data = json_decode($request->getContent(), true);

        // Récupérer les références aux services Firestore
        $firestore = $this->firebaseService->getFirestore();

        // Récupérer la référence à la collection "Reservation" de Firestore
        $reservationReference = $firestore->collection("Reservation")->document($reservationId);

        // Vérifier si la réservation existe
        $reservationSnapshot = $reservationReference->snapshot();
        if (!$reservationSnapshot->exists()) {
            return new JsonResponse([
                "message" => "La réservation spécifiée n'existe pas",
                "userId" => $userId,
                "reservationId" => $reservationId
            ], 404);
        }

        // Mettre à jour les champs de la réservation si nécessaire
        $updates = [];
        if (isset($data["message_reservation"])) {
            $updates[] = ["path" => "message_reservation", "value" => $data["message_reservation"]];
        }
        if (isset($data["date_debut"])) {
            $updates[] = ["path" => "date_debut", "value" => $data["date_debut"]];
        }
        if (isset($data["date_fin"])) {
            $updates[] = ["path" => "date_fin", "value" => $data["date_fin"]];
        }
        if (isset($data["statut_reservation"])) {
            $updates[] = ["path" => "statut_reservation", "value" => $data["statut_reservation"]];
        }

        // Effectuer les mises à jour
        $reservationReference->update($updates);

        // Retourner un objet JSON contenant un message de succès
        return new JsonResponse([
            "message" => "Réservation mise à jour avec succès",
            "userId" => $userId,
            "reservationId" => $reservationId
        ]);
    }

    /**
     * @Route("reservation/{userId}/{reservationId}", methods={"DELETE"})
     */
    public function deleteReservation($userId, $reservationId)
    {
        // Récupérer les références aux services Firestore
        $firestore = $this->firebaseService->getFirestore();

        // Récupérer la référence à la collection "Reservation" de Firestore
        $reservationReference = $firestore->collection("Reservation")->document($reservationId);

        // Vérifier si la réservation existe
        $reservationSnapshot = $reservationReference->snapshot();
        if (!$reservationSnapshot->exists()) {
            return new JsonResponse([
                "message" => "La réservation spécifiée n'existe pas",
                "userId" => $userId,
                "reservationId" => $reservationId
            ], 404);
        }

        // Supprimer la réservation
        $reservationReference->delete();

        // Retourner un objet JSON contenant un message de succès
        return new JsonResponse([
            "message" => "Réservation supprimée avec succès",
            "userId" => $userId,
            "reservationId" => $reservationId
        ]);
    }



 


    
}