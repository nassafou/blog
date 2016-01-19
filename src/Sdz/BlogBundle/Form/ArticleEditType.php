<?php

namespace Sdz\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ArticleEditType extends ArticleType // Ici on hérite de ArticleType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //On fait appel à la méthode buildForm du parent, qui va ajouter tous les champs à $builder
        parent::buildForm($builder, $options);
        
        //On supprime ce que on ne veut pas dans le formulaire de modification
        $builder->remove('date');
    }
    
    /**
     * @return string
     */
    // On modifie cette methode car les deux formulaires doivent avoir un nom different
    public function getName()
    {
        return 'sdz_blogbundle_article_edittype';
    }
}
