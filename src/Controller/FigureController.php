<?php

namespace App\Controller;

use App\Repository\FigureRepository;
use App\Entity\Figure;
use App\Entity\User;
use App\Form\FigureType;
use App\Form\EditFigureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/figure', name: 'app_figure_')]
class FigureController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(FigureRepository $figureRepository): Response
    {
        $figures = $figureRepository->findAll();
        return $this->render('figure/index.html.twig', [
            'figures' => $figures,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]

    public function create(Request $request, FigureRepository $figureRepository): Response
    {
        $figure = new Figure();

        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Traitement des données du formulaire
            $figureRepository->save($figure, true);
            // Redirection vers une autre page par exemple
            return $this->redirectToRoute('app_figure_index', status: Response::HTTP_SEE_OTHER);
        }
        return $this->render(
            'figure/new.html.twig',
            [
                'form' => $form,
                'figure' => $figure
            ]
        );
    }

    #[Route('/edit/{slug}', name: 'edit', methods: ['GET', 'POST'])]
    #[IsGranted("", subject: "user")]
    public function edit(Figure $figure, Request $request, FigureRepository $figureRepository): Response
    {
        $form = $this->createForm(EditFigureType::class, $figure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $figureRepository->flush();

            return $this->redirectToRoute('app_figure_show', [
                'id' => $figure->getId()
            ], Response::HTTP_SEE_OTHER);
        }
        return $this->render(
            'figure/edit.html.twig',
            [
                'form' => $form->createView(),
                'figure' => $figure
            ]
        );
    }

    #[Route('/{slug}', name: 'show', methods: ['GET'])]
    public function show(Figure $figure): Response
    {

        return $this->render('figure/show.html.twig', [
            'figure' => $figure
        ]);
    }
    #[Route('/delete/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Figure $figure, FigureRepository $figureRepository): Response
    {
        // if (!$this->security->getUser()) {
        //     throw new AccessDeniedHttpException('Vous devez être connecté pour accéder à cette page.');
        // }

        // // Vérifie si l'utilisateur actuel est l'auteur de la figure
        // if ($this->security->getUser() !== $figure->getAuthor()) {
        //     throw new AccessDeniedHttpException('Vous n\'êtes pas autorisé à supprimer cette figure.');
        // }

        $figureRepository->remove($figure);
        $figureRepository->flush();

        // Redirige vers la page d'index après la suppression
        return $this->redirectToRoute('app_figure_index');
    }
}
