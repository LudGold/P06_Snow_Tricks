<?php

namespace App\Controller;

use App\Repository\FigureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class MainController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function homepage(FigureRepository $figureRepository): Response
    {
        $figures = $figureRepository->findAll();
        return $this->render('home_page.html.twig', [
            'figures' => $figures,
            
        ]);
    }

    #[Route('/load-more-figures', name: 'load_more_figures')]
    public function loadMoreFigures(Request $request, FigureRepository $figureRepository): JsonResponse
    {
        $offset = $request->query->getInt('offset', 0);
        $limit = 15;

        $figures = $figureRepository->findBy([], [], $limit, $offset);
        $remaining = count($figureRepository->findAll()) - ($offset + $limit);

        $html = $this->renderView('figure/_figures.html.twig', ['figures' => $figures]);

        return new JsonResponse(['html' => $html, 'remaining' => $remaining]);
    }
}