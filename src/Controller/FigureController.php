<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\Comment;
use App\Repository\FigureRepository;
use App\Repository\CommentRepository;
use App\Repository\CategoryRepository;
use App\Form\FigureType;
use App\Form\CommentType;
use App\Service\ImageUploader;
use App\Service\VideoUploader;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/figure', name: 'app_figure_')]
class FigureController extends AbstractController
{
    private $slugger;
    private $commentRepository;
    private $figureRepository;

    public function __construct(SluggerInterface $slugger, CommentRepository $commentRepository, FigureRepository $figureRepository)
    {
        $this->slugger = $slugger;
        $this->commentRepository = $commentRepository;
        $this->figureRepository = $figureRepository;
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(FigureRepository $figureRepository): Response
    {

        $figures = $figureRepository->findBy([], null);
        return $this->render('figure/index.html.twig', [
            'figures' => $figures,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request, FigureRepository $figureRepository, ImageUploader $imageUploader, VideoUploader $videoUploader): Response
    {
        $figure = new Figure();
        $form = $this->createForm(FigureType::class, $figure, ['validation_groups' => ['create']]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if ($user) {
                $figure->setAuthor($user);
                $slug = $this->slugger->slug($figure->getName());
                $figure->setSlug(strtolower($slug));

                $imageUploader->uploadImages($figure);
                $videoUploader->uploadVideos($figure);
                // Définir la première image téléchargée comme image de couverture
                $images = $figure->getImages();
                if (count($images) > 0) {
                    $coverImage = $images[0];
                }
                $figureRepository->save($figure, true);

                $this->addFlash('success', 'La figure a bien été créée');

                return $this->redirectToRoute('app_figure_index', [], Response::HTTP_SEE_OTHER);
            }
        }
        return $this->render('figure/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{slug}', name: 'edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    #[IsGranted('', subject: 'figure')]
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
            $figure->setSlug($this->slugger->slug($figure->getName())->lower());
            
            $figureRepository->save($figure, true);

            return $this->redirectToRoute('app_figure_show', [
                'slug' => $figure->getSlug()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('figure/edit.html.twig', [
            'form' => $form->createView(),
            'figure' => $figure,
        ]);
    }

    
    #[Route('/{slug}', name: 'show', methods: ['GET', 'POST'])]
    public function show(string $slug, Figure $figure, Request $request,  CategoryRepository $categoryRepository): Response
    {
        // Récupérer la figure par son slug
        $figure = $this->figureRepository->findOneBySlug($slug);
        if (!$figure) {
            throw $this->createNotFoundException('La figure spécifiée est introuvable.');
        }

        // Pagination des commentaires
        $page = $request->query->getInt('page', 1);
        $limit = 3;

        // Utiliser la méthode de pagination du CommentRepository
        $commentsData = $this->commentRepository->findPaginatedByFigure($figure, $page, $limit);

        // Récupérer les catégories
        $categories = $categoryRepository->findAll();

        // Formulaire de commentaires
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        // Rendre la vue avec les commentaires paginés
        return $this->render('figure/show.html.twig', [
            'figure' => $figure,
            'categories' => $categories,
            'form' => $form->createView(),
            'comments' => $commentsData['data'],
            'currentPage' => $commentsData['page'],
            'totalPages' => $commentsData['totalPages']
        ]);
    }



    #[Route('/delete/{id}', name: 'delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    #[IsGranted('', subject: 'figure')]
    public function delete(Figure $figure, FigureRepository $figureRepository): Response
    {
        $figureRepository->remove($figure, true);
        $this->addFlash('success', 'La figure a bien été supprimée');

        return $this->redirectToRoute('app_figure_index');
    }
    }

