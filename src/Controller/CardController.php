<?php

namespace App\Controller;

use App\Entity\Card;
use App\Form\CardType;
use App\Repository\CardRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Form\ThemeType;
/**
 * @Route("admin/card")
 */
class CardController extends AbstractController
{
    /**
     * @Route("/", name="card_index", methods={"GET","POST"})
     */
    public function index(CardRepository $cardRepository, Request $request ): Response
    {
        $form=$this->createForm(ThemeType::class);

        $form->handleRequest($request);
        $theme=$request->get('theme');
   
        $session=$this->get('session')->set('titre', $theme);
        if($session!=$theme){
            session_unset();
            $session=$this->get('session')->set('titre', $theme);          
        }
        $session=$this->get('session')->get('titre');
       
        if(isset($session)){ 
            $cards = $cardRepository->findByThemeField($session);
            return $this->render('card/index.html.twig', [
                'cards' => $cards, 
                'form' => $form->createView(),
                'session' => $session['titre']     
            ]); 
         }        
        
        return $this->render('card/index.html.twig', [                    
            'form' => $form->createView(),
            'cards' => $cardRepository->findAll(),                      
        ]);     
    }



    /**
     * @Route("/new", name="card_new", methods={"GET","POST"})
     */
    public function new(Request $request, SluggerInterface $slugger): Response
    {
        $card = new Card();
        $form = $this->createForm(CardType::class, $card);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $card->setImage($newFilename);
            }
            $theme = $form->get('theme')->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($card);
            $entityManager->flush();
                
            $card->setTheme($theme);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($card);           
            $entityManager->flush();            
            

            return $this->redirectToRoute('card_index');
        }

        return $this->render('card/new.html.twig', [
            'card' => $card,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="card_show", methods={"GET"})
     */
    public function show(Card $card): Response
    {
        return $this->render('card/show.html.twig', [
            'card' => $card,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="card_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Card $card, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(CardType::class, $card);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $card->setImage($newFilename);
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('card_index');
        }

        return $this->render('card/edit.html.twig', [
            'card' => $card,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="card_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Card $card): Response
    {
        if ($this->isCsrfTokenValid('delete'.$card->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($card);
            $entityManager->flush();
        }

        return $this->redirectToRoute('card_index');
    }
   
}
