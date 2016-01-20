<?php

namespace Sdz\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sdz\BlogBundle\Antispam\SdzAntispam;
use Sdz\BlogBundle\Entity\Article;
use Sdz\BlogBundle\Entity\Image;
use Sdz\BlogBundle\Entity\Commentaire;
use Sdz\BlogBundle\Entity\ArticleCompetence;
use Sdz\BlogBundle\Form\ArticleType;
use Sdz\BlogBundle\Form\ArticleEditType;

class BlogController extends Controller
{
    public function indexAction($page)
    {
        
        
        // Pour recuperer la liste de tous les articles on va utiliser findAll
        $articles = $this->getDoctrine()
                         ->getManager()
                         ->getRepository('BlogBundle:Article')
                         ->getArticles(3, $page);
        return $this->render('BlogBundle:Blog:index.html.twig', array('articles'   => $articles,
                                                                      'page'       => $page,
                                                                      'nombrePage' => ceil(count($articles)/3)
                                                                      ));

    }
    public function ajouterAction()
    {
        //On crée un objet Article
        $article = new Article();
        
        // On crée le formulaire grace à l'ArticleType
        $form = $this->createForm( new ArticleType, $article );
        
        //On recupere la requete
        $request=  $this->get('request');
        
        // si c'est un formulaire de type post ou on verifie qu'elle est 
        if ($request->getMethod() == 'POST')
        {
            // On fait le lien  requete <-> Formulaire
            
            $form->bind($request);
            
            // On verifie que les valeurs entrées sont correctes
            
            
            
            if($form->isValid() )
            {
                // Ici on traite manuellement le uploadé
                //$article->getImage()->upload();
                
                // On enregistre notre objet $article dans la base de données 
                $em = $this->getDoctrine()->getManager();
                $em->persist($article);
                $em->flush();
                
                // On definit un message flush
                $this->get('session')->getFlashBag()->add('info', 'Article bien ajouté');
                
                //On redirige vers la page de visualisation de l'article nouvellement crée
                return $this->redirect($this->generateUrl('blog_voir',
                                                          array('id' => $article->getId())));
                
            }
            
        }
        
          
          
          //A ce stade :
          // - Soit la requete est de type  GET, donc le visiteur vient d'arriver sur la page et veut
          //voir le formulaire
          //- Soit la requete est de type POST, mais le formulaire n'est pas valide, donc on l'affiche de nouveau
          

        return $this->render('BlogBundle:Blog:ajouter.html.twig',
                             array( 'form' => $form->createView() )); 
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
    
    public function modifierAction(Article $article)
    {
        
        //entity manager
        //$em = $this->getDoctrine()->getManager();
        //On récupère l'article
        //$article = $em->getRepository('BlogBundle:Article')->find($id);
        
        // On utilise le ArticleEditType
        $form = $this->createForm(new ArticleEditType, $article );
        
        $request = $this->getRequest();
        
        if($request ->getMethod() == 'POST')
        {
            $form->bind($request);
            if($form->isValid() )
            {
                //on enregistre l'article
                $em = $this->getDoctrine()->getManager();
                $em->persist($article);
                $em->flush();
                
                // on defnit un message flush
                $this->get('session')->getFlashBag()->add('info', 'Article bien modifier');
                
                return $this->redirect($this->generateUrl('blog_voir', array('id' => $article->getId())) );
            }
        }  
           return $this->render('BlogBundle:Blog:modifier.html.twig',
           array(
             'article' => $article,
             'form' => $form->createView()
              ));
    }
    
    #Suppression des categorie d'un article
    public function supprimerAction(Article $article )
    {
        // On crée un formulaire vide , qui ne contiendra que le champ CSRF
        // Cela permet de protéger la suppression d'article contre cette famille
        
        $form = $this->createFormBuilder()->getForm();
        
        $request = $this->getRequest();
        
        if($request ->getMethod() == 'POST')
        {
            $form->bind($request);
            
            if($form->isValid() )
            {
                //on enregistre l'article
                $em = $this->getDoctrine()->getManager();
                $em->remove($article);
                $em->flush();
                
                // on defnit un message flush
                $this->get('session')->getFlashBag()->add('info', 'Article bien supprimé');
                
                return $this->redirect($this->generateUrl('blog_accueil') );
            }
        }
         // Si la requete est en GET, on affiche une page de confirmation avant de supprimer
           return $this->render('BlogBundle:Blog:supprimer.html.twig',
           array(
             'article' => $article,
             'form' => $form->createView()
              ));
    }
    
    public function menuAction($nombre)
    {
        $liste = $this->getDoctrine()
                      ->getManager()
                      ->getRepository('BlogBundle:Article')
                      ->findBy(
                               array(), //Pas de critère
                               array('date' => 'desc'),//trie par date décroissante
                               $nombre, //on selection un nombre
                               0 // a partir du premier
                               );
        return $this->render('BlogBundle:Blog:menu.html.twig', array('liste_articles' => $liste ));
    }
    public function formulaireAction($id)
    {
        return $this->render('BlogBundle:Blog:formulaire.html.twig', array('name' => $name));
    }
    public function testAction()
    {
        $listeArticles = $this->getDoctrine()
                              ->getManager()
                              ->getRepository('BlogBundle:Article')
                              ->getArticleAvecCommentaires();
            
            foreach($listeArticles as $article )
            {
                //Ne déclenche pas de requête : les commentaires sont déja chargés
                //Faire une boucle pour les affichés tous
                $article->getCommentaires();
                
                            
            }
    }
}
