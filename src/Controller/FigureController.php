<?php

namespace App\Controller;

use App\Repository\FigureRepository;
use App\Entity\Figure;
use App\Repository\VideoRepository;
use App\Repository\CategoryRepository;
use App\Form\FigureType;
use App\Service\ImageUploader;
use App\Service\VideoUploader;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/figure', name: 'app_figure_')]
class FigureController extends AbstractController
{

    private $slugger;
    private $entityManager;
    private $categoryRepository;

    public function __construct(SluggerInterface $slugger, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository)
    {

        $this->slugger = $slugger;
        $this->entityManager = $entityManager;
        $this->categoryRepository = $categoryRepository;
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(FigureRepository $figureRepository): Response
    {
        $figures = $figureRepository->findAll();
        return $this->render('figure/index.html.twig', [
            'figures' => $figures,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function create(Request $request, FigureRepository $figureRepository, ImageUploader $imageUploader, VideoUploader $videoUploader): Response
    {
        $figure = new Figure();
        $form = $this->createForm(FigureType::class, $figure, ['validation_groups' => ['create']]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            // Vérifiez si l'utilisateur est connecté
            if ($user) {
                // Attribuez l'utilisateur actuellement connecté comme auteur de la figure
                $figure->setAuthor($user);
                // Générez le slug de la figure
                $slug = $this->slugger->slug($figure->getName());
                $figure->setSlug(strtolower($slug));

                $imageUploader->uploadImages($figure);
                $videoUploader->uploadVideos($figure);

                // Enregistrez la figure en base de données
                $figureRepository->save($figure, true);

                $this->addFlash('success', 'La figure a bien été créée');

                // Redirection vers une autre page
                return $this->redirectToRoute('app_figure_index', [], Response::HTTP_SEE_OTHER);
            }
        }
        return $this->render('figure/new.html.twig', [
            'form' => $form->createView(),

        ]);
    }

    #[Route('/edit/{slug}', name: 'edit', methods: ['GET', 'POST'])]
    #[IsGranted("", subject: "figure")]
    #[IsGranted("ROLE_USER")]
    public function edit(Figure $figure, FigureRepository $figureRepository, Request $request, ImageUploader $imageUploader, VideoUploader $videoUploader): Response
    {
        foreach ($figure->getImages() as $image) {
            $image->setFile(
                new File($this->getParameter('images_directory') . '/' . $image->getImageName())
            );
        }

        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageUploader->uploadImages($figure);
            $videoUploader->uploadVideos($figure);

            // Enregistrez la figure en base de données
            $figureRepository->save($figure, true);

            return $this->redirectToRoute('app_figure_show', [
                'slug' => $figure->getSlug()
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
    public function show(string $slug, FigureRepository $figureRepository, VideoRepository $videoRepository): Response

    {
        $figure = $figureRepository->findOneBy(['slug' => $slug]);
        $categories = $this->categoryRepository->findAll();
        $videos = $videoRepository->findBy(['figure' => $figure]);

        $images = $figure->getImages();
        if (!$figure) {
            throw $this->createNotFoundException('Figure non trouvée');
        }
        return $this->render('figure/show.html.twig', [
            'figure' => $figure,
            'images' => $images,
            'videos' => $videos,
            'categories' => $categories
        ]);
    }
    #[Route('/delete/{id}', name: 'delete', methods: ['POST'])]
    #[IsGranted("", subject: "figure")]
    #[IsGranted("ROLE_USER")]
    public function delete(Figure $figure, FigureRepository $figureRepository): Response
    {
        $figureRepository->remove($figure, true);
        $this->addFlash('success', 'La figure a bien été supprimée');

        // Redirige vers la page d'index après la suppression
        return $this->redirectToRoute('app_figure_index');
    }
}
