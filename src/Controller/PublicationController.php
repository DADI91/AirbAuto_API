<?php

namespace App\Controller;

use App\Services\FirebaseService;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Kreait\Firebase\Storage;

class PublicationController extends AbstractController
{
    private $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }




    /**
     * @Route("publications/{userId}", methods={"POST"})
     */
    public function createPublication(Request $request, $userId)
    {
        // Récupérer les données du document à partir de la requête
        $data = json_decode($request->getContent(), true);

        // Récupérer les références aux services Firestore et Storage
        $firestore = $this->firebaseService->getFirestore();

        // Récupérer la référence à la collection "documents" de Firestore

        $userReference = $firestore->collection("Publication");

        $documentData = [
            "Titre_Publication" => $data["Titre_Publication"] ?? "",
            "Description_Publication" => $data["Description_Publication"] ?? "",
            "img_Publication1" => $data["img_Publication1"] ?? "",
            "img_Publication2" => $data["img_Publication2"] ?? "",
            "img_Publication3" => $data["img_Publication3"] ?? "",
            "Marque_Vehicule" => $data["Marque_Vehicule"] ?? "",
            "Date_Immatriculation" => $data["Date_Immatriculation"] ?? "",
            "Note_Publication" => $data["Note_Publication"] ?? "",
            "Etat_Publication" => $data["Etat_Publication"] ?? "",
            "ID_Type_Publication" => $data["ID_Type_Publication"],
            "Id_User" => $userId,
        ];

        $newDocument = $userReference->add($documentData);

    
       
        return new JsonResponse(["message" => "Publication créé avec succès pour l'utilisateur", "userId" => $userId, "documentId" => $newDocument->id()]);
    }




    /**
     * @Route("publications/{userId}", methods={"GET"})
     */
    public function getAllPublicationsByUserId($userId)
    {
        // Récupérer la référence aux services Firestore
        $firestore = $this->firebaseService->getFirestore();

        // Récupérer la référence à la collection "Publication" de Firestore
        $userReference = $firestore->collection("Publication");

        // Récupérer toutes les publications de l'utilisateur
        $publications = [];
        $query = $userReference->where("Id_User", "==", $userId)->documents();
        foreach ($query as $document) {
            $publications[] = $document->data();
        }

        return new JsonResponse([
            "message" => "Publications récupérées avec succès pour l'utilisateur",
            "userId" => $userId,
            "publications" => $publications,
        ]);
    }

    /**
     * @Route("publication_id/{documentId}", methods={"GET"})
     */
    public function getPublicationById( $documentId)
    {
        // Récupérer la référence aux services Firestore
        $firestore = $this->firebaseService->getFirestore();

        // Récupérer la référence à la collection "Publication" de Firestore
        $userReference = $firestore->collection("Publication");

        // Récupérer la publication spécifique par son ID
        $document = $userReference->document($documentId)->snapshot();
        $publicationData = $document->data();

        return new JsonResponse([
            "message" => "Publication récupérée avec succès",
            "documentId" => $documentId,
            "publication" => $publicationData,
        ]);
    }




    

    /**
     * @Route("publications", methods={"GET"})
     */
    public function getAllPublications()
    {
        // Récupérer la référence aux services Firestore
        $firestore = $this->firebaseService->getFirestore();

        // Récupérer la référence à la collection "Publication" de Firestore
        $userReference = $firestore->collection("Publication");

        // Récupérer toutes les publications de l'utilisateur
        $publications = [];
        $query = $userReference->documents();
        foreach ($query as $document) {
            $publications[] = $document->data();
        }

        return new JsonResponse([
            "message" => "Toutes publications récupérées avec succès ",
            "publications" => $publications,
        ]);
    }

    

    

    /**
     * @Route("publications/{userId}/{documentId}", methods={"DELETE"})
     */
    public function deletePublication($userId, $documentId)
    {
        // Récupérer la référence aux services Firestore
        $firestore = $this->firebaseService->getFirestore();

        // Récupérer la référence à la collection "Publication" de Firestore
        $userReference = $firestore->collection("Publication");

        // Supprimer le document correspondant à l'ID spécifié
        $userReference->document($documentId)->delete();

        return new JsonResponse([
            "message" => "Publication supprimée avec succès pour l'utilisateur",
            "userId" => $userId,
            "documentId" => $documentId,
        ]);
    }

    /**
     * @Route("publication/{userId}/{documentId}", methods={"PUT"})
     */
    public function updatePublication(Request $request, $userId, $documentId)
    {
        // Récupérer les données de la publication à partir de la requête
        $data = json_decode($request->getContent(), true);

        // Récupérer les références aux services Firestore et Storage
        $firestore = $this->firebaseService->getFirestore();
        $bucket = $this->firebaseService->getBucket();

        // Récupérer la référence à la collection "Publication" de Firestore
        $userReference = $firestore->collection("Publication")->document($documentId);

        // Récupérer les données actuelles de la publication
        $publicationData = $userReference->snapshot()->data();

        // Créer un tableau pour stocker les mises à jour à effectuer
        $updates = [];

        // Mettre à jour les champs de la publication si nécessaire
        $fields = [
            "Titre_Publication",
            "Description_Publication",
            "Marque_Vehicule",
            "Date_Immatriculation",
            "Note_Publication",
            "Etat_Publication",
        ];

        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $updates[] = ["path" => $field, "value" => $data[$field]];
            }
        }

        // Mettre à jour les images de la publication si nécessaire
        $imageFields = [
            "img_Publication1",
            "img_Publication2",
            "img_Publication3"
        ];

        foreach ($imageFields as $field) {
            if (isset($data[$field]) && $data[$field] != $publicationData[$field]) {

                // Supprimer l'ancienne image de Firebase Storage
                $oldImageUrl = $publicationData[$field];
                $oldImagePath = $this->getImagePathFromUrl($oldImageUrl, $bucket);

                if ($oldImagePath) {
                    $bucket->object($oldImagePath)->delete();
                }

                // Stocker la nouvelle image dans Firebase Storage et récupérer l'URL

                // Ajouter la mise à jour de l'image
                $updates[] = ["path" => $field, "value" => $data[$field]];
            }
        }

        // Effectuer les mises à jour
        $userReference->update($updates);

        // Retourner un objet JSON contenant un message de succès
        return new JsonResponse([
            "message" => "Publication mise à jour avec succès",
            "userId" => $userId,
            "documentId" => $documentId
        ]);
    }



    private function getImagePathFromUrl($imageUrl, $bucket){

        $parsedUrl = parse_url($imageUrl);

        if (!isset($parsedUrl['query'])) {
            return null;
        }

        $queryParams = [];
        parse_str($parsedUrl['query'], $queryParams);
    
        $path = ltrim($parsedUrl['path'], '/');
        $bucketName = $bucket->name();
    
        if (strpos($path, $bucketName) === 0) {
            $path = substr($path, strlen($bucketName) + 1); // Remove the bucket name and the following '/' from the path
        }
    
        if (isset($queryParams['GoogleAccessId']) && $queryParams['GoogleAccessId'] === 'firebase-adminsdk-zjfum@airbauto-91130.iam.gserviceaccount.com') {
            return $path;
        }
    
        return null;
    }



    
}