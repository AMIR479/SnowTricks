<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Figure;
use App\Entity\Image;
use App\Form\CommentType;
use App\Form\FigureType;
use App\Repository\CommentRepository;
use App\Repository\FigureRepository;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;




/**
 * @Route("/figure")
 */
class FigureController extends AbstractController
{
    /**
     * @Route("/", name="app_figure_index", methods={"GET"})
     */
    public function index(FigureRepository $figureRepository): Response
    {
        return $this->render('figure/index.html.twig', [
            'figures' => $figureRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_figure_new", methods={"GET", "POST"})
     */
    public function new(Request $request, FigureRepository $figureRepository): Response
    {
        $figure = new Figure();
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //On récupère les images transmises
            $images = $form->get('images')->getData();

            dd($images);
            //On boucle sur les images
            foreach($images as $image){
                //on génére un nouveau  nom de fichier
                $fichier = md5(uniqid()) . '.' . $image->guessExtension(); 
            
                // On copie le fichier dans le dossier images
            
                $image->move(
                    $this->getParameter('images_directory', $fichier)
                );
                //on stocke l'image dans la BDD
                $img = new Image();
                $img->setPhotoFilename($fichier);
                $figure->addImage($img);

            }
            $figure->setUser($this->getUser());
            $figureRepository->add($figure, true);
        

            return $this->redirectToRoute('app_figure_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('figure/new.html.twig', [
            'figure' => $figure,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_figure_show", methods={"GET", "POST"})
     */
    public function show(Request $request, Figure $figure, CommentRepository $commentRepository ): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setFigure($figure);
            $commentRepository->add($comment, true);
            return $this->redirectToRoute('app_figure_show', 
            ['id'=>$figure->getId()], 
                Response::HTTP_SEE_OTHER);
        }
        return $this->render('figure/show.html.twig', [
            'figure' => $figure,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_figure_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Figure $figure, FigureRepository $figureRepository): Response
    {
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //On récupère les images transmises
            $images = $form->get('image')->getData();
            //On boucle sur les images
            foreach($images as $image){
                //on génére un nouveau  nom de fichier
                $fichier = md5(uniqid()) . '.' . $image->guessExtension(); 
                // On copie le fichier dans le dossier images
                $image->move(
                    $this->getParameter('images_directory', $fichier)
                );
                //on stocke l'image dans la BDD
                $img = new Image();
                $img->setPhotoFilename($fichier);
                $figure->addImage($img);

               
            }

            $figureRepository->add($figure, true);

            return $this->redirectToRoute('app_figure_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('figure/edit.html.twig', [
            'figure' => $figure,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_figure_delete", methods={"POST"})
     */
    public function delete(Request $request, Figure $figure, FigureRepository $figureRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$figure->getId(), $request->request->get('_token'))) {
            $figureRepository->remove($figure, true);
        }

        return $this->redirectToRoute('app_figure_index', [], Response::HTTP_SEE_OTHER);
    }

   
   /**
    * @Route("/supprime/image/{id}", name="figure_delete_image", methods={"DELETE", "POST"})
  */
    public function deleteImage(Image $image, Request $request, ImageRepository $imageRepository, EntityManagerInterface $entityManager)
    {
        $data = json_decode($request->getContent(), true);
        // On vérifie si le token est valide
        if($this->isCsrfTokenValid('delete'.$image->getId(), $data['_token']))
        {
            //On récupère le nom de l'image
            $nom = $image->getPhotoFilename();
            //On supprime le fichier
            unlink($this->getParameter('images_directory').'/'.$nom);
           //On supprime l'entrée de la BDD 
           $imageRepository->remove($image);

            // On répond en Json
            return new JsonResponse(['success' => 1]);
        }
        else
        {
            return new JsonResponse(['error' => 'token invalid'], 404);
        }
    }
}
