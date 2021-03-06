<?php

namespace Sdz\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Image
 *
 * @ORM\Table(name="image")
 * @ORM\Entity(repositoryClass="Sdz\BlogBundle\Entity\ImageRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Image
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255)
     */
    private $alt;
    
    private $file;
    
    // On ajout ce attribut pour y stocker le nom du fichier temporairement
    
    private $tempFilename;
    
    // On modifie le setter de file, pour prendre en compte l'upload d'un fichier lorsqu'il en existe déja un autre
    public function setFile(UploadedFile $file)
    {
        $this->file = $file;
        
        //On vérifie si on avait deja un fichier pour cette entité
        if(null !== $this->url){
            // On sauvegarde l'extension du fichier pour le supprimer plus tard
            $this->tempFilename = $this->url;
            
            // On réinitialise les valeurs des attributs url et alt
            $this->url = null;
            $this->alt = null;
        }
    }

    /**
     *@ORM\PrePersist()
     *@ORM\PreUpdate()
     */
    
    public function preUpload()
    {
        //si jamais il n'y a pas de fichier (champ facultatif)
        if(null === $this->file ){
            return;
        }
        
        // Le nom du fichier est son ID, on doit juste stocker également son extension
        //Pour faire propre, on devrait renommer cet attribut en << extension >>, plutot que << url >>
        $this->url = $this->file->guessExtension();
        
        // Et on génère l'attribut alt de la balise <img>, à la valeur du nom du fichier sur le PC de l'internaute
        $this->alt = $this->file->getClientOriginalName();
    }
    
    /**
     *@ORM\PostPersist()
     *@ORM\PostUpdate()
     */
    
    public function upload()
    {
        //si jamais il n'y a pas de fichier (champ facultatif)
        if(null === $this->file ){
            return;
        }
        
        // Si on avait un ancien fichier on le supprime
        
        if( null !== $this->tempFilename){
            $oldFile = $this->getUploadRootDir().'/'.$this->id.'.'.$this->tempFilename;
            if(file_exists($oldFile)){
                unlink($oldFile);
            }
        }
        
        //On garde le nom original du fichier de l'internaute
        //$name = $this->file->getClientOriginalName();
        
        // On deplace le fichier envoyé dans le repertouire de notre choix
        
        $this->file->move(
                          $this->getUploadRootDir(), // Le repertoire de destination
                          $this->id.'.'.$this->url // le nom du fichier a creer, ici << id.extension >>
                          );
        
        // On sauvegarde le nom de fichier dans notre attribut $url
        //$this->url = $name;
        
        //On crée également le futur atribut alt de notre balise <img>
        //$this->alt = $name;
    }
    
    /**
     *@ORM\PreRemove()
     */
    public function preRemoveUpload()
    {
        // On sauvegarde temporairement le nom du fichier car il dépend de Id
        $this->tempFilename = $this->getUploadDir().'/'.$this->id.'.'.$this->url;
    }
    
    /**
     *@ORM\PostRemove()
     */
    public function removeUpload()
    {
        // En PostRemouve on n'a pas acces a l'id, on utilise notre nom sauvegardé
        if(file_exists($this->tempFilename)){
            // On supprime le fichier
            unlink($this->tempFilename );
        }
    }
    
    public function getUploadDir()
    {
        //On retourne le chemin relatif vers l'image pour un navigateur
        return 'uploads/img';
    }
    
    public function getUploadRootDir()
    {
        // On retourne le chemin relatif vers l'image pour notre code PHP
        
        return DIR  .'/../../../../web/'.$this->getUploadDir();
    }
    
    public function getWebPath()
    {
        return $this->getUploadDir().'/'.$this->getId().'.'.$this->getUrl();
    }

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
     * Set url
     *
     * @param string $url
     * @return Image
     */
    public function setUrl($url)
    {
        $this->url = $url;
    
        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set alt
     *
     * @param string $alt
     * @return Image
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;
    
        return $this;
    }

    /**
     * Get alt
     *
     * @return string 
     */
    public function getAlt()
    {
        return $this->alt;
    }
}