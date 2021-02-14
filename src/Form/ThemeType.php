<?php

namespace App\Form;

use App\Entity\Theme;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Data\SearchData;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Repository\ThemeRepository;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvents;

class ThemeType extends AbstractType
{
    private $session;
    private $repo;

    public function __construct(SessionInterface $session, ThemeRepository $repo)
    {
        $this->session = $session;
        $this->repo = $repo;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $session = $this->session->get('session');
        $theme = $this->repo->findTheme($session);

        $builder
        ->add('titre', EntityType::class, [
            'label' => false,
            'class' => Theme::class,
            'choice_label' => 'titre',
            'placeholder' => $theme[0]->getTitre(),
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('p')
                    ->orderBy('p.id', 'ASC');          
            },

            
 ]);
    
        
       
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        
        $resolver->setDefaults([
            'data_class' => Theme::class,
           
        ]);
    }
}
