<?php

namespace App\Controller;

use App\Repository\FigureRepository;
use App\Entity\Figure;
use App\Entity\Image;
use App\Entity\Comment;
use App\Entity\Video;
use App\Repository\VideoRepository;
use App\Repository\ImageRepository;
use App\Repository\CommentRepository;
use App\Repository\CategoryRepository;
use App\Form\FigureType;
use App\Form\VideoType;
use App\Form\ImageType;
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


    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
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
    public function show(?Figure $figure, CommentRepository $commentRepository, CategoryRepository $categoryRepository): Response
    {
        $limit = 3;
        $comments = $commentRepository->findBy(['figure' => $figure], ['id' => 'ASC'], $limit);

        $categories = $categoryRepository->findAll();

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        if (!$figure) {
            throw $this->createNotFoundException('Figure non trouvée');
        }
        return $this->render('figure/show.html.twig', [
            'figure' => $figure,
            'categories' => $categories,
            'form' => $form->createView(),
            'comments' => $comments,
            'limit' => $limit,
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

    #[Route('/load-more-comments', name: 'load_more_comments', methods: ['GET'])]
    public function loadMoreComments(Request $request, CommentRepository $commentRepository, FigureRepository $figureRepository): Response
    {
        $figureSlug = $request->query->get('figureSlug');
        dd($figureSlug);
        $offset = $request->query->getInt('offset', 0);
        $limit = 3;

        $figure = $figureRepository->findOneBy(['slug' => $figureSlug]);

        if (!$figure) {
            return new Response('OULA', 404);
        }

        $comments = $commentRepository->findBy(['figure' => $figure], ['id' => 'ASC'], $limit, $offset);

        if (!$comments) {
            return new Response('', 204); // No Content
        }

        return $this->render('figure/_comments.html.twig', [
            'comments' => $comments,
        ]);
    }
    #[Route('/edit-cover/{id}', name: 'edit_cover', methods: ['POST'])]
    public function editCover(Figure $figure, Request $request, FigureRepository $figureRepository): Response
    {
        $newCoverImageId = $request->request->get('cover_image_id');
        $newCoverImage = $figure->getImages()->filter(function (Image $image) use ($newCoverImageId) {
            return $image->getId() == $newCoverImageId;
        })->first();

        if ($newCoverImage) {
            // Logique pour changer l'image de couverture
            $figure->getImages()->removeElement($newCoverImage);
            $figure->getImages()->add($newCoverImage);  // Ajoute en première position

            $figureRepository->save($figure, true);
            $this->addFlash('success', 'L\'image de couverture a bien été modifiée');
        } else {
            $this->addFlash('error', 'Image non trouvée');
        }

        return $this->redirectToRoute('app_figure_edit', ['slug' => $figure->getSlug()]);
    }
    #[Route('/edit/image/{id}', name: 'edit_image', methods: ['GET', 'POST'])]
    public function editImage(Image $image, Request $request, ImageRepository $imageRepository): Response
    {
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si besoin d'uploader une nouvelle image, implémente cette logique ici

            $imageRepository->save($image, true); // Sauvegarde des modifications

            $this->addFlash('success', 'Image modifiée avec succès.');

            // Redirection vers la page de détails de la figure par exemple
            return $this->redirectToRoute('app_figure_show', ['slug' => $image->getFigure()->getSlug()]);
        }

        return $this->render('figure/edit_image.html.twig', [
            'form' => $form->createView(),
            'image' => $image,
        ]);
    }
    #[Route('/delete-image/{id}', name: 'delete_image', methods: ['POST'])]
    public function deleteImage(Request $request, Image $image, FigureRepository $figureRepository): Response
    {
        if ($this->isCsrfTokenValid('delete_image' . $image->getId(), $request->request->get('_token'))) {
            $figure = $image->getFigure();
            $figure->removeImage($image);
            $figureRepository->save($figure, true);

            $this->addFlash('success', 'Image supprimée avec succès');
        } else {
            $this->addFlash('error', 'Token CSRF invalide');
        }

        return $this->redirectToRoute('app_figure_edit', ['slug' => $figure->getSlug()]);
    }
    #[Route('/edit-video/{id}', name: 'edit_video', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function editVideo(Video $video, Request $request, VideoRepository $videoRepository): Response
    {
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $videoRepository->save($video, true);

            $this->addFlash('success', 'La vidéo a été modifiée avec succès.');

            return $this->redirectToRoute('app_figure_show', [
                'slug' => $video->getFigure()->getSlug(),
            ]);
        }

        return $this->render('figure/edit_video.html.twig', [
            'form' => $form->createView(),
            'video' => $video,
            
        ]);
    }
    #[Route('/delete-video/{id}', name: 'delete_video', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function deleteVideo(Video $video, VideoRepository $videoRepository): Response
    {
        $figureSlug = $video->getFigure()->getSlug();

        $videoRepository->remove($video, true);

        $this->addFlash('success', 'La vidéo a été supprimée avec succès.');

        return $this->redirectToRoute('app_figure_show', [
            'slug' => $figureSlug,
        ]);
    }
}
