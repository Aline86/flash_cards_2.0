<?php
namespace App\Controller;
use App\Data\SearchData;
use App\Form\ThemeType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormBuilderInterface;
use App\Entity\Card;
use App\Entity\Theme;
use App\Repository\CardRepository;
use App\Repository\ThemeRepository;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/")
 */
class FrontController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }


    /**
     * @Route("/",
     *
     *  name="front", methods={"GET", "POST"})
     */
    public function cards(CardRepository $cardRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $session = $request->get('theme');
        $this->session->set('session', $session);
        $form=$this->createForm(ThemeType::class);
        $form->handleRequest($request);
        $page = 1;
        $nbPerPage = 1;
        $cards = $cardRepository->myFindAllWithPaging($page, $nbPerPage, $session['titre']);

        $this->session->set('session', $session);


       // dd($total_pages);
        // $nbTotalPages = intval(ceil(count($articles) / $nbPerPage));cards = $cardRepository->FindCard($page, $session);
        $nbTotalPages = intval(ceil(count($cards) / $nbPerPage));
        return $this->render('front/index.html.twig', [
            'form' => $form->createView(),
            'cards' => $cards,
            'total' => $nbTotalPages,
            'page' => $page
        ]);

    }
     /**
     * @Route("/{page}",
     * options={"expose": true},
     * requirements={"page":"\d+"},
     *  name="ajax", methods={"GET", "POST"})
     */
    public function ajax(CardRepository $cardRepository, Request $request, PaginatorInterface $paginator, $page): Response
    {
        $form=$this->createForm(ThemeType::class);
        $form->handleRequest($request);
        $session = $this->session->get('session');


        if($request->isXmlHttpRequest())
        {     

            $cards = $cardRepository->myFindAllWithPaging1($page, 1, $session['titre']);
            $encoders = [new JsonEncoder()];
            //On instancie le normalizer pour convertir la collection en tableau
            $normalizers = [new ObjectNormalizer()];
            //On fait la conversion en json
            //On instancie le convertisseur
            $serializer = new Serializer($normalizers, $encoders);
            //on convertit en json
            $jsonContent = $serializer->serialize($cards, 'json', [
                'circular_reference_handler' => function($object){
                    return $object->getId();
                }
            ]);
            //On instancie la réponse
            $response = new Response($jsonContent);
            //On ajoute l'entête HTTP
            $response->headers->set('Content-Type', 'application/json');
            // On envoie la réponse 
            
            return $response;
           

        }
    }
}