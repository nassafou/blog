<?php

namespace Sdz\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class ArticleType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Metre tous les champs sans la 
        $builder
            //->add('dateEdition')
            //->add('publication')
            ->add('date')
            ->add('auteur')
            ->add('contenu')
            ->add('titre')
            //->add('categories')
            ->add('image', new ImageType())
            ->add('categories', 'entity', array('class'      => 'BlogBundle:Categorie',
                                                'property'   => 'nom',
                                             'multiple'      => true,
                                             'expandded'     => false ))
        ;
        
        
        // On recupere la factory (usine)
        $factory = $builder->getFormFactory();
        
        //On ajoute une fonction qui va écouter l'évenement PRE_SET_DATA
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA, //Ici, on definit l'evenement qui nous interesse
            function(FormEvent $event) use ($factory){
                //Ici on definit une fonction qui sera executée lors de l'evenement
                $article = $event->getData();
                // Cette condition est importante, on en reparle plus
                if( null === $article) {
                    return; // On sort de la fonction lorsque l'article vaut null 
                }
                // Si l'article n'est pas encore publié on ajoute le champ publication
                if (false === $article->getPublication()) {
                    $event->getForm()->add(
                        $factory->createNamed('publication', 'checkbox', null, array('required' => false))
                    );
                }else { // Sinon, on le supprime
                    $event->getForm()->remove('publication');
                }
            }
        );
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sdz\BlogBundle\Entity\Article'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sdz_blogbundle_article';
    }
}
