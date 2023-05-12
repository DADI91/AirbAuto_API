<?php

namespace App\Controller;

use App\Services\FirebaseService;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Kreait\Firebase\Storage;

class DocumentController extends AbstractController
{
    private $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }
    
    


    /**
     * @Route("user/documents/{userId}", methods={"POST"})
     */
    public function createDocument(Request $request, $userId)
    {
        // Récupérer les données du document à partir de la requête
        $data = json_decode($request->getContent(), true);

        // Récupérer les références aux services Firestore et Storage
        $firestore = $this->firebaseService->getFirestore();
        $bucket = $this->firebaseService->getBucket();

        // Récupérer la référence à la collection "documents" de Firestore

        $userReference = $firestore->collection("Users")->document($userId);



        // Stocker les images dans Firebase Storage et récupérer leurs URL

        $userReference->update([
            ["path" => "documents.img_permis", "value" => $data["img_permis"] ?? ""],
            ["path" => "documents.permis_valide", "value" => $data["permis_valide"] ?? ""],
            ["path" => "documents.img_Identite", "value" => $data["img_Identite"] ?? ""],
            ["path" => "documents.Identite_valide", "value" => $data["Identite_valide"] ?? ""],
            ["path" => "documents.img_justificatif_domicile", "value"  => $data["img_justificatif_domicile"] ?? ""],
            ["path" => "documents.justificatif_dom_valide", "value" => $data["justificatif_dom_valide"] ?? ""],
            ["path" => "documents.img_assurence_vehicule",  "value" => $data["img_assurence_vehicule"] ?? ""],
            ["path" => "documents.assurence_valide", "value" => $data["assurence_valide"] ?? ""],
        ]);
        return new JsonResponse(["message" => "Document créé avec succès pour l'utilisateur", "userId" => $userId]);
    }


    /**
     * @Route("/user/documents/{userId}", methods={"GET"})
     */
    public function getDocumentUserById($userId)
    {
        // Récupérer la référence à l'utilisateur dans la collection "Users" de Firestore
        $userReference = $this->firebaseService->getFirestore()->collection("Users")->document($userId);

        // Vérifier si l'utilisateur existe
        if (!$userReference->snapshot()->exists()) {
            return new JsonResponse(["error" => "L'utilisateur n'existe pas"]);
        }
        // Récupérer un instantané du document
        $snapshot = $userReference->snapshot();

        // Récupérer les données du document sous forme de tableau associatif
        $data = $snapshot->data();

        // Récupérer les données du document pour l'utilisateur
        //$documentData = $userReference->data()["documents"];

        // Renvoyer les données sous forme de réponse JSON
        return new JsonResponse($data["documents"]);
    }



    /**
     * @Route("user/documents/{userId}", methods={"PUT"})
     */
    public function updateDocument(Request $request, $userId)
    {
        // Récupérer les données du document à partir de la requête
        $data = json_decode($request->getContent(), true);

        // Récupérer les références aux services Firestore et Storage
        $firestore = $this->firebaseService->getFirestore();
        $bucket = $this->firebaseService->getBucket();

        // Récupérer la référence à la collection "Users" de Firestore
        $userReference = $firestore->collection("Users")->document($userId);

        // Récupérer les données actuelles de l'utilisateur
        $userData = $userReference->snapshot()->data();

        // Créer un tableau pour stocker les mises à jour à effectuer
        $updates = [];

        // Mettre à jour les champs de validité si nécessaire
        $fields = ["permis_valide", "Identite_valide", "justificatif_dom_valide", "assurence_valide"];
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $updates[] = ["path" => "documents.$field", "value" => $data[$field]];
            }
        }

        // Mettre à jour les images si nécessaire
        $imageFields = ["img_permis", "img_Identite", "img_justificatif_domicile", "img_assurence_vehicule"];
        foreach ($imageFields as $field) {


            if (isset($data[$field]) && $data[$field] != $userData["documents"][$field]) {

                // Supprimer l'ancienne image de Firebase Storage
                $oldImageUrl = $userData["documents"][$field];
                $oldImagePath = $this->getImagePathFromUrl($oldImageUrl, $bucket);
                //print("URL 1");
                //print($oldImagePath);

                if ($oldImagePath) {
                    $bucket->object($oldImagePath)->delete();
                }

                // Stocker la nouvelle image dans Firebase Storage et récupérer l'URL

                // Ajouter la mise à jour de l'image
                $updates[] = ["path" => "documents.$field", "value" => $data[$field]];
            }
        }

        // Effectuer les mises à jour
        $userReference->update($updates);

        // Retourner un objet JSON contenant un message de succès
        return new JsonResponse(["message" => "Document mis à jour avec succès pour l'utilisateur", "userId" => $userId]);
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