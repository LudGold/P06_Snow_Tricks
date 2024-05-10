<?php

namespace App\Controller;

use App\Repository\FigureRepository;
use App\Entity\Figure;
use App\Form\FigureType;
use App\Service\ImageUploader;
use App\Service\VideoUploader;
use App\Entity\Image;
use App\Security\Voter\UserVoter;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/figure', name: 'app_figure_')]
class FigureController extends AbstractController
{

    private $slugger;
    private $entityManager;

    public function __construct(SluggerInterface $slugger, EntityManagerInterface $entityManager)
    {

        $this->slugger = $slugger;
        $this->entityManager = $entityManager;
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
                // Récupérez les fichiers d'image téléchargés
                $imageFiles = $form->get('images')->getData();
           
                foreach ($imageFiles as $imageFile) {
                    if ($imageFile instanceof UploadedFile) {
                        try {
                            // Utilisez le service ImageUploader pour télécharger le fichier
                            $newFilename = $imageUploader->upload($imageFile);
                            ($newFilename);
                            // Créez une entité Image et associez-la à la figure
                            $image = new Image();
                            $image->setPath($newFilename);
                            $image->setImageName($imageFile->getClientOriginalName());
                            // Utilisez le nom d'origine du fichier comme nom d'image
                            $figure->addImage($image);
                        } catch (FileException $e) {
                            // Gérer les erreurs de téléchargement de fichier
                        }
                    }
                }
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
    //         $figure->setAuthor($user);
    //         $figure->setName($form->get('name')->getData());
    //         $figure->setDescription($form->get('description')->getData());
    //         $figure->setCategory($form->get('category')->getData());

    //         // Récupérez les images téléchargées
    //         $imageFiles = $form->get('images')->getData();
    //         foreach ($imageFiles as $imageFile) {
    //             if ($imageFile instanceof UploadedFile) {
    //                 try {
    //                     // Utilisez le service ImageUploader pour télécharger le fichier
    //                     $newFilename = $imageUploader->upload($imageFile);
    //                     // Créez une entité Image si nécessaire et associez-la à la figure
    //                     $image = new Image();
    //                     $image->setPath($newFilename);
    //                     $image->setimageName($newFilename);
    //                     // Ajoutez l'image à la collection d'images de la figure
    //         $figure->addImage($image);

    //                 } catch (FileException $e) {
    //                     // Gérer les erreurs de téléchargement de fichier
    //                 }
    //             }
    //         }
    //             // Enregistrez la figure en base de données
    //         $figureRepository->save($figure, true);

    //         $this->addFlash('success', 'La figure a bien été créée');

    //         // Redirection vers une autre page
    //         return $this->redirectToRoute('app_figure_index', status: Response::HTTP_SEE_OTHER);
    //     }

    //     // Si le formulaire n'est pas soumis ou n'est pas valide, affichez le formulaire
    //     return $this->render('figure/new.html.twig', [
    //         'form' => $form->createView(),
    //     ]);
    // }






    #[Route('/edit/{slug}', name: 'edit', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_USER", subject: "figure")]
    public function edit(Figure $figure, Request $request): Response
    {
        // Vérifier si l'utilisateur actuellement connecté est l'auteur de la figure
        if ($this->getUser() !== $figure->getAuthor()) {
            throw new AccessDeniedException("Vous n'êtes pas autorisé à modifier cette figure.");
        }
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->flush();

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
    public function show(string $slug, FigureRepository $figureRepository): Response
    {
        $figure = $figureRepository->findOneBy(['slug' => $slug]);
        $images = $figure->getImages();
        if (!$figure) {
            throw $this->createNotFoundException('Figure non trouvée');
        }
        return $this->render('figure/show.html.twig', [
            'figure' => $figure,
            'images' => $images,
        ]);
    }
    #[Route('/delete/{id}', name: 'delete', methods: ['GET', 'POST'])]
    public function delete(Figure $figure, FigureRepository $figureRepository): Response
    {
        $figureRepository->remove($figure, true);


        // Redirige vers la page d'index après la suppression
        return $this->redirectToRoute('app_figure_index');
    }
}
