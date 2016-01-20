<?php

namespace Sdz\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Sdz\BlogBundle\Entity\Tag;
use Symfony\Component\Validator\ExecutionContextInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Article
 *
 * @ORM\Table(name="article")
 * @ORM\Entity(repositoryClass="Sdz\BlogBundle\Entity\ArticleRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Assert\Callback(methods={"contenuValide"})
 * @UniqueEntity(fields="titre", message="Un article existe déja avec ce titre.")
 */
class Article
{
    /**
     *@ORM\OneToMany(targetEntity="Sdz\BlogBundle\Entity\Commentaire", mappedBy="article")
     *@ORM\JoinColumn(nullable=false)
     */
    private $commentaires; // commentaire prend "s", car un article a plusieurs commentaire
    
    /**
     *@ORM\ManyToMany(targetEntity="Sdz\BlogBundle\Entity\Categorie", cascade={"persist"})
     */
    private $categories;
    
    /**
     *@ORM\Column(type="date", nullable=true)
     */
    private $dateEdition;
    
    
    //comme la propriété $categories doit être un arrayCollection, on doit la definir dans un constructeur
    
    
    /**
     *@ORM\OneToOne(targetEntity="Sdz\BlogBundle\Entity\Image", cascade={"persist"})
     *@Assert\Valid()
     */
    private $image;
    
    /**
     *@ORM\Column(name="publication", type="boolean")
     */
    private $publication;
    
    //definition des attributs
    public function __construct()
    {
        $this->date = new \DateTime(); // Par defaut, la date de l'article est la date d'aujourd'hui 
        $this->publication = true;
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
        $this->commentaires = new \Doctrine\Common\Collections\ArrayCollection();
    }
    public function contenuValide(ExecutionContextInterface $context)
    {
        $mots_interdits = array('échec', 'abandon');
        
        // On verfie que le contenu ne contient pas l'un des mots
        if(preg_match('#'.implode('|', $mots_interdits).'#', $this->getContenu())){
            //La regle est violé, on definit le l'erreur et son message
            // 1 er argument : on dit quel attribut l'erreur concerne, ici <<contenu >>
            // 2 e argument : le message d'erreur
            
            $context->addViolationAt('contenu','Contenu invalide car il contient un mot inderdit, array(), null');
        }
    }
    
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     * @Assert\DateTime()
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="auteur", type="string", length=255)
     * @Assert\Length(
     *        min = "2",
     *        max = "50",
     *        minMessage = "votre nom doit faire au moins {{ limit }} caractères ",
     *        maxMessage= "Votre nom ne peut pas être plus long que {{ limit }} caractères "
     *        )
     */
    private $auteur;

    /**
     * @var string
     *
     * @ORM\Column(name="contenu", type="string", length=500)
     * @Assert\NotBlank()
     */
    private $contenu;

    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=255, unique=true)
     * @Assert\Length(
               min = "10",
               max = "50",
               minMessage = "Votre titre doit faire au {{ limit }} caractères",
               maxMessage = "Votre titre ne peut pas être plus long que {{ limit }} caractères "
     )
     */
    private $titre;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Article
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set auteur
     *
     * @param string $auteur
     * @return Article
     */
    public function setAuteur($auteur)
    {
        $this->auteur = $auteur;
    
        return $this;
    }

    /**
     * Get auteur
     *
     * @return string 
     */
    public function getAuteur()
    {
        return $this->auteur;
    }

    /**
     * Set contenu
     *
     * @param string $contenu
     * @return Article
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;
    
        return $this;
    }

    /**
     * Get contenu
     *
     * @return string 
     */
    public function getContenu()
    {
        return $this->contenu;
    }

    /**
     * Set titre
     *
     * @param string $titre
     * @return Article
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;
    
        return $this;
    }

    /**
     * Get titre
     *
     * @return string 
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set publication
     *
     * @param boolean $publication
     * @return Article
     */
    public function setPublication($publication)
    {
        $this->publication = $publication;
    
        return $this;
    }

    /**
     * Get publication
     *
     * @return boolean 
     */
    public function getPublication()
    {
        return $this->publication;
    }

    /**
     * Set image
     *
     * @param \Sdz\BlogBundle\Entity\Image $image
     * @return Article
     */
    public function setImage(\Sdz\BlogBundle\Entity\Image $image = null)
    {
        $this->image = $image;
    
        return $this;
    }

    /**
     * Get image
     *
     * @return \Sdz\BlogBundle\Entity\Image 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Add categories
     *
     * @param \Sdz\BlogBundle\Entity\Categorie $categories
     * @return Article
     */
    public function addCategorie(\Sdz\BlogBundle\Entity\Categorie $categories)
    {
        $this->categories[] = $categories;
    
        return $this;
    }

    /**
     * Remove categories
     *
     * @param \Sdz\BlogBundle\Entity\Categorie $categories
     */
    public function removeCategorie(\Sdz\BlogBundle\Entity\Categorie $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Add commentaires
     *
     * @param \Sdz\BlogBundle\Entity\Commentaire $commentaires
     * @return Article
     */
    public function addCommentaire(\Sdz\BlogBundle\Entity\Commentaire $commentaires)
    {
        $this->commentaires[] = $commentaires;
        $commentaires->setArticle($this); //imbrication
    
        return $this;
    }

    /**
     * Remove commentaires
     *
     * @param \Sdz\BlogBundle\Entity\Commentaire $commentaires
     */
    public function removeCommentaire(\Sdz\BlogBundle\Entity\Commentaire $commentaires)
    {
        $this->commentaires->removeElement($commentaires);
    }

    /**
     * Get commentaires
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCommentaires()
    {
        return $this->commentaires;
    }

    /**
     * Set dateEdition
     *
     * @param \DateTime $dateEdition
     * @return Article
     */
    public function setDateEdition($dateEdition)
    {
        $this->dateEdition = $dateEdition;
    
        return $this;
    }

    /**
     * Get dateEdition
     *
     * @return \DateTime 
     */
    public function getDateEdition()
    {
        return $this->dateEdition;
    }
}