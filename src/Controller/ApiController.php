<?php

namespace App\Controller;

use App\Service\ImportService;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class ApiController extends FrontendController
{
    #[Route('/api/import/cars/{assetId}', name: 'import-cars')]
    public function importCarsFromAsset(int $assetId, ImportService $importService): Response
    {
        $importService->importCarsFromAsset($assetId);

        return $this->json([
            'message' => 'All data imported',
        ]);
    }
}
