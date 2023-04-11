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
     * Stocke une image dans Firebase Storage et renvoie son URL.
     *
     * @param string $base64Image L'image encodée en base64
     * @param  $storage La référence au service Firebase Storage
     * @param string $folder Le dossier dans lequel stocker l'image (facultatif)
     * @return string L'URL de l'image stockée
     */
    private function storeImage($binaryImage, $bucket, $folder = "images"): string
    {
        // Générer un nom de fichier unique pour l'image
        $filename = uniqid() . ".jpg";
    
        // Générer un jeton d'accès unique
        $accessToken = bin2hex(random_bytes(16));
    
        // Créer une référence à l'emplacement de stockage de l'image dans Firebase Storage
        $reference = $bucket->object($folder . "/" . $filename);
    
        // Stocker l'image dans Firebase Storage
        $stream = fopen('php://memory','r+');
        fwrite($stream, $binaryImage);
        rewind($stream);
    
        $bucket->upload($stream, [
            'name' => $folder . '/' . $filename,
            'predefinedAcl' => 'publicRead',
            'metadata' => [
                'contentType' => 'image/jpeg',
                'metadata' => [
                    'firebaseStorageDownloadTokens' => $accessToken,
                ],
            ],
        ]);
    
        // Récupérer l'URL de l'image stockée avec le jeton d'accès
        $url = $reference->signedUrl(new \DateTime('+1 week'));
        $url = str_replace('?GoogleAccessId', '?alt=media&token=' . $accessToken . '&GoogleAccessId', $url);
    
        return $url;
    }
    
    
    private function getImageBinary($imageData): string
    {
        if (filter_var($imageData, FILTER_VALIDATE_URL)) {
            // Image URL
            return file_get_contents($imageData);
        } elseif (preg_match('/^data:image\/(\w+);base64,/', $imageData, $matches)) {
            // Base64-encoded image
            return base64_decode(substr($imageData, strlen($matches[0])));
        }

        return "";
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

        $collectionReference = $firestore->collection("documents");
        $userReference = $firestore->collection("Users")->document($userId);


        // Convertir les données d'image en binaire
        $img_permis_binary = $this->getImageBinary($data["img_permis"]);
        $img_Identite_binary = $this->getImageBinary($data["img_Identite"]);
        $img_justificatif_domicile_binary = $this->getImageBinary($data["img_justificatif_domicile"]);
        $img_assurence_vehicule_binary = $this->getImageBinary($data["img_assurence_vehicule"]);

        // Stocker les images dans Firebase Storage et récupérer leurs URL
        $img_permis_url = $img_permis_binary ? $this->storeImage($img_permis_binary, $bucket) : "";
        $img_Identite_url = $img_Identite_binary ? $this->storeImage($img_Identite_binary, $bucket) : "";
        $img_justificatif_domicile_url = $img_justificatif_domicile_binary ? $this->storeImage($img_justificatif_domicile_binary, $bucket) : "";
        $img_assurence_vehicule_url = $img_assurence_vehicule_binary ? $this->storeImage($img_assurence_vehicule_binary, $bucket) : "";

        $userData = $userReference->snapshot()->data();

        $documentData = [
            "img_permis" => $img_permis_url,
            "permis_valide" => $data["permis_valide"] ?? "",
            "img_Identite" => $img_Identite_url,
            "Identite_valide" => $data["Identite_valide"] ?? "",
            "img_justificatif_domicile" => $img_justificatif_domicile_url,
            "justificatif_dom_valide" => $data["justificatif_dom_valide"] ?? "",
            "img_assurence_vehicule" => $img_assurence_vehicule_url,
            "assurence_valide" => $data["assurence_valide"] ?? "",
        ];

        $userReference->update([
            ["path" => "documents.img_permis", "value" => $img_permis_url],
            ["path" => "documents.permis_valide", "value" => $data["permis_valide"] ?? ""],
            ["path" => "documents.img_Identite", "value" => $img_Identite_url],
            ["path" => "documents.Identite_valide", "value" => $data["Identite_valide"] ?? ""],
            ["path" => "documents.img_justificatif_domicile", "value" => $img_justificatif_domicile_url],
            ["path" => "documents.justificatif_dom_valide", "value" => $data["justificatif_dom_valide"] ?? ""],
            ["path" => "documents.img_assurence_vehicule", "value" => $img_assurence_vehicule_url],
            ["path" => "documents.assurence_valide", "value" => $data["assurence_valide"] ?? ""],
        ]);

        return new JsonResponse(["message" => "Document créé avec succès pour l'utilisateur", "userId" => $userId]);

        /*$newDocument = $collectionReference->add([
            "img_permis" => $img_permis_url,
            "permis_valide" => $data["permis_valide"] ?? "",
            "img_Identite" => $img_Identite_url,
            "Identite_valide" => $data["Identite_valide"] ?? "",
            "img_justificatif_domicile" => $img_justificatif_domicile_url,
            "justificatif_dom_valide" => $data["justificatif_dom_valide"] ?? "",
            "img_assurence_vehicule" => $img_assurence_vehicule_url,
            "assurence_valide" => $data["assurence_valide"] ?? "",
        ]);

        // Retourner un objet JSON contenant un message de succès et l'identifiant du nouveau document créé
        return new JsonResponse(["message" => "Document créé avec succès", "document" => $newDocument->id()]);*/
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
                print("URL 1");
                print($oldImagePath);

                if ($oldImagePath) {
                    $bucket->object($oldImagePath)->delete();
                }

                // Stocker la nouvelle image dans Firebase Storage et récupérer l'URL
                $newImageBinary = $this->getImageBinary($data[$field]);
                $newImageUrl = $newImageBinary ? $this->storeImage($newImageBinary, $bucket) : "";

                // Ajouter la mise à jour de l'image
                $updates[] = ["path" => "documents.$field", "value" => $newImageUrl];
            }
        }

        // Effectuer les mises à jour
        $userReference->update($updates);

        // Retourner un objet JSON contenant un message de succès
        return new JsonResponse(["message" => "Document mis à jour avec succès pour l'utilisateur", "userId" => $userId]);
    }

    private function getImagePathFromUrl($imageUrl, $bucket){
        
        $parsedUrl = parse_url($imageUrl);
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