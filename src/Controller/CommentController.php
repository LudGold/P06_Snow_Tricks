<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Symfony\Component\Form\FormFactoryInterface;
use App\Repository\FigureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Psr\Log\LoggerInterface;

class CommentController extends AbstractController
{

    private $figureRepository;


    public function __construct(FigureRepository $figureRepository)
    {

        $this->figureRepository = $figureRepository;
    }
    #[Route('/comment', name: 'app_comment')]
    public function index(): Response
    {
        $figures = $this->figureRepository->findAll();
        return $this->render('comment/index.html.twig', [
            'figures' => $figures,
        ]);
    }
    #[Route('/comment/new/{figure_slug}', name: 'comment_new', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, $figure_slug, FigureRepository $figureRepository, CommentRepository $commentRepository, FormFactoryInterface $formFactory): Response
    {
        $figure = $figureRepository->findOneBySlug($figure_slug);
        if (!$figure) {
            throw $this->createNotFoundException('Figure non trouvée');
        }

        if ($request->getMethod() === 'POST' && $request->request->has('comment')) {
            $data = $request->request->get('comment[content]');
            $comment = new Comment($data);
            $comment->setFigure($figure);
            $comment->setUser($this->getUser());
            $form = $formFactory->create(CommentType::class, $comment);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $commentRepository->save($comment, true);
                $this->addFlash('success', 'Votre commentaire a bien été ajouté.');
                return $this->redirectToRoute('app_figure_show', ['slug' => $figure->getSlug()]);
            }
        }

        $this->addFlash('danger', 'Votre commentaire n\'a pas pu être ajouté.');
        return $this->redirectToRoute('app_figure_show', ['slug' => $figure->getSlug()]);
    }
  

}