<?php

namespace App\Controller;

use App\Services\FirebaseService;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Kreait\Firebase\Storage;

class UploadMediaController extends AbstractController
{
    private $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }


    /**
     * @Route("/upload_media/{userId}", methods={"POST"})
     */
    public function uploadFile(Request $request, $userId)
    {

        // Récupérer le fichier depuis la requête
        $file = $request->files->get('file');
        
        // Récupérer la raison pour laquel seras utiliser l

        $folderSelect = $request->get('typeFile');
        // Vérifier que le fichier est valide
        if (!$file) {
            return new JsonResponse(["message" => "No file uploaded"], 400);
        }
        if ($file->getError()) {
            return new JsonResponse(["message" => "Error uploading file"], 500);
        }

        // Récupérer les références aux services Firestore et Storage
        $firestore = $this->firebaseService->getFirestore();
        $bucket = $this->firebaseService->getBucket();

        // Stocker le fichier dans Firebase Storage
        $url = $this->storeFile($file->getRealPath(), $bucket, $file->getClientOriginalExtension(), $folderSelect , $userId);

        return new JsonResponse(["url" => $url]);
    }

    private function storeFile($filePath, $bucket, $extension, $folderSelect , $userId  )
    {
        // Générer un nom de fichier unique pour le fichier
        $filename = uniqid('', true) . '.' . $extension;

        // Générer un jeton d'accès unique
        $accessToken = bin2hex(random_bytes(16));

        // Créer une référence à l'emplacement de stockage du fichier dans Firebase Storage
        $reference = $bucket->object($userId . '/' . $folderSelect . "/" . $filename);

        // Stocker le fichier dans Firebase Storage
        $bucket->upload(fopen($filePath, 'r'), [
            'name' => $userId . '/'. $folderSelect . '/' . $filename,
            'predefinedAcl' => 'publicRead',
            'metadata' => [
                'metadata' => [
                    'firebaseStorageDownloadTokens' => $accessToken,
                ],
            ],
        ]);

        // Récupérer l'URL du fichier stocké avec le jeton d'accès
        $url = $reference->signedUrl(new \DateTime('+1 week'));
        $url = str_replace('?GoogleAccessId', '?alt=media&token=' . $accessToken . '&GoogleAccessId', $url);

        return $url;
    }

}