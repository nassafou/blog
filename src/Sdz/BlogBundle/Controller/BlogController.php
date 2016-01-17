<?php

namespace Sdz\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sdz\BlogBundle\Antispam\SdzAntispam;
use Sdz\BlogBundle\Entity\Article;
use Sdz\BlogBundle\Entity\Image;
use Sdz\BlogBundle\Entity\Commentaire;
use Sdz\BlogBundle\Entity\ArticleCompetence;

class BlogController extends Controller
{
    public function indexAction($page)
    {
        if( $page < 1 )
        {
            throw $this->createNotFoundException('La page inexistante  (page = '.$page.')' );
        }
        $articles = array(
                array(   'id'       => 1,
                         'titre'    => 'Un bon week-end ',
                         'auteur'   =>  'YOZ',
                         'date'     => new \DateTime()),
                array(   'id'       => 2,
                         'titre'    => 'Symfony2 ',
                         'auteur'   =>  'Pass',
                         'date'     => new \DateTime()),
                array(   'id'       => 5,
                         'titre'    => 'Pirate ',
                         'auteur'   =>  'Man',
                         'date'     => new \DateTime()),
                );
        
        return $this->render('BlogBundle:Blog:index.html.twig', array('articles' => $articles ));

    }
    public function ajouterAction()
    {
        // On récupére l'EntityManager
       $em = $this->getDoctrine()
           ->getManager();
      // Création de l'entité Article
      $article = new Article();
      $article->setTitre('Mon dernier weekend');
      $article->setContenu("C'était vraiment super et on s'est bien amusé.");
      $article->setAuteur('winzou');
    // Dans ce cas, on doit créer effectivement l'article en bdd
    //pour lui assigner un id
    // On doit faire cela pour pouvoir enregistrer les
    //ArticleCompetence par la suite
    $em->persist($article);
    $em->flush(); // Maintenant, $article a un id défini
     // Les compétences existent déjà, on les récupère depuis la bdd
    $liste_competences = $em->getRepository('BlogBundle:Competence')
                            ->findAll(); // Pour l'exemple, notre Article contient toutes les Competences
    // Pour chaque compétence
     foreach($liste_competences as $i => $competence)
     {
    // On crée une nouvelle « relation entre 1 article et 1 compétence »
      $articleCompetence[$i] = new ArticleCompetence;
     // On la lie à l'article, qui est ici toujours le même
     $articleCompetence[$i]->setArticle($article);
    // On la lie à la compétence, qui change ici dans la boucle foreach
     $articleCompetence[$i]->setCompetence($competence);
    // Arbitrairement, on dit que chaque compétence est requise au niveau 'Expert'
    $articleCompetence[$i]->setNiveau('Expert');
    // Et bien sûr, on persiste cette entité de relation, propriétaire des deux autres relations
    $em->persist($articleCompetence[$i]);
    }
      // On déclenche l'enregistrement
     $em->flush();
     // ... reste de la méthode
     
    if ($this->getRequest()->getMethod() == 'POST') {
      $this->get('session')->getFlashBag()->add('info', 'Article bien enregistré');
         return $this->redirect( $this->generateUrl('blog_voir', array('id' => $article->getId())) );
          }

        return $this->render('BlogBundle:Blog:ajouter.html.twig');
    
    }
    
    public function voirAction($id)
    {
        // On crée l'entity manager
        $em = $this->getDoctrine()->getManager();
        
        
          //receperer l'entité
          $article = $em->getRepository('BlogBundle:Article')->find($id);
        
        //ou si on ne trouve aucun article
        if($article === null )
        {
            throw $this->createNotFoundException('Article[id='.$id.'] inexistant.');
        }
        // On recupere la liste des commentaires
        $liste_commentaires = $em->getRepository('BlogBundle:Commentaire')->findAll();
        
        //On recupere les articleCompetence pour l'article $article
        $liste_articleCompetence = $em->getRepository('BlogBundle:ArticleCompetence')
                                      ->findByArticle($article->getId());
        
        return $this->render('BlogBundle:Blog:voir.html.twig', array('article' => $article,
                                                                    'liste_commentaires' => $liste_commentaires,
                                                                    'liste_articleCompetence' => $liste_articleCompetence
                                                                     ));
    }
    
    public function modifierAction($id_article)
    {
        
        //entity manager
        $em = $this->getDoctrine()->getManager();
        //On récupère l'article
        $article = $em->getRepository('BlogBundle:Article')->find($id_article);
        
        if($article === null)
         {
           throw $this->NotFoundException('Article[id='.$id_article.'] inexistant.');   
         }
         
         $categories = $em->getRepository('BlogBundle:Categorie')->findAll();
         
         //On cree une boucle pour lier les categories aux articles
         foreach( $liste_categories as  $categories)
         {
            $article->addCategorie($categorie);
         }
         
         //pas besoin de persiste
         
         //On flush et on declenche l'enregistre
         $em->flush();
        
               return new Response('OK');
    }
    
    #Suppression des categorie d'un article
    public function supprimerAction($id)
    {
        //On recupere l'entity manager
        $em = $this->getDoctrine()->getManager();
        
        //On recupere l'entité correspondant à l'id
         $article = $em->getRepository('BlogBundle:Article')->find($id);
         
         if( $article === null )
         {
            throw $this->NotFoundException('Article[id='.$id.'] inexistant');
         }
        //on cherche toutes les categories:
        $liste_categories = $em->getRespository('BlogBundle:Categorie')->findAll();
        // On enleve toutes les categorie de l'article
        foreach( $liste_categories as $categorie )
        {
            // On appele la methode removeCategorie()
            
            $article->removeCategorie($categorie);
        }
        
        //pas de persist
        // on flush
        $article->removeCategorie($categorie);
        return new Response('OK');
        
       // return $this->render('BlogBundle:Blog:supprimer.html.twig', array('name' => $name));
    }
    
    public function menuAction($nombre)
    {
        $liste = array(
                array(   'id'       => 1,
                         'titre'    => 'Un bon week-end '),
                array(   'id'       => 2,
                         'titre'    => 'Symfony2'),
                array(   'id'       => 5,
                         'titre'    => 'Pirate '),
                );
        return $this->render('BlogBundle:Blog:menu.html.twig', array('liste_articles' => $liste ));
    }
    public function formulaireAction($id)
    {
        return $this->render('BlogBundle:Blog:formulaire.html.twig', array('name' => $name));
    }
}
