<?php
namespace Sdz\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *@ORM\Entity
 */
class ArticleCompetence
{
/**
 *@ORM\Id
 *@ORM\ManyToOne(targetEntity="Sdz\BlogBundle\Entity\Article")
 */
private $article;

/**
 *@ORM\Id
 *@ORM\ManyToOne(targetEntity="Sdz\BlogBundle\Entity\Competence")
 */
private $competence;

/**
 *@ORM\Column();
 */
private $niveau;

// Getter et setter pour l'entité Article
public function setArticle(\Sdz\BlogBundle\Entity\Article
$article)
{
$this->article = $article;
}
public function getArticle()
{
return $this->article;
}
// Getter et setter pour l'entité Competence
public function setCompetence(\Sdz\BlogBundle\Entity\Competence
$competence)
{
$this->competence = $competence;
}
public function getCompetence()
{
return $this->competence;
}
// On définit le getter/setter de l'attribut « niveau »
public function setNiveau($niveau)
{
$this->niveau = $niveau;
}
public function getNiveau()
{
return $this->niveau;
}

}
