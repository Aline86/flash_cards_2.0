<?php
namespace App\Controller;
use App\Data\SearchData;
use App\Form\ThemeType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormBuilderInterface;
use App\Entity\Card;
use App\Entity\Theme;
use App\Repository\CardRepository;
use App\Repository\ThemeRepository;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * @Route("/")
 */
class FrontController extends AbstractController
{

    /**
    * @Route("/", name="front", methods={"GET", "POST"})
    */
    public function index(CardRepository $cardRepository, Request $request, PaginatorInterface $paginator ): Response
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
            $ajax= $cardRepository->findByThemeField($session);
            $card=$paginator->paginate($ajax, 
            $request->query->getInt('page', 1),
            1);
            return $this->render('front/index.html.twig', [
                'cards' => $card,
                'form' => $form->createView(),
                'session' => $session['titre']     
            ]); 
         }        
        /*$ajax=$this->ajax($request, $russeRepository);*/
        $donnees=$this->getDoctrine()->getRepository(Card::class)->findAll();
        $card=$paginator->paginate($donnees, 
        $request->query->getInt('page', 1),
        1);
        return $this->render('front/index.html.twig', [                    
            'form' => $form->createView(),
            'cards' => $card                         
        ]);     
    }
}