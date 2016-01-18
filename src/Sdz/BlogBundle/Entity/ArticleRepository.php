<?php

namespace Sdz\BlogBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ArticleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArticleRepository extends EntityRepository
{
    public function myFindAll()
    {
        return $this->createQueryBuilder('a')
                    ->getQuery()
                    ->getResult();
    
    }
    
    // Joindre les Articles par catégorie
    public function getAvecCategories(array $nom_categories)
    {
      $qb = $this->createQueryBuilder('a');
      // On fait une jointure avec l'entité Catégorie, avec pour alias << c >>
      $qb ->join('a.categories', 'c')
          ->where($qb->expr()->in('c.nom', $nom_categories)); //puis on filter sur le nom des catégories a l'aide d'un IN
          //Enfin, On retourn le resultat
          return $qb->getQuery()
                    ->getResult();
    }
    
    //Joindre les Commentaires a leur article
    
    public function getArticleAvecCommentaires()
    {
        $qb = $this->createQueryBuilder('a')
                   ->leftJoin('a.commentaires', 'c')
                   ->addSelect('c');
        
        return $qb->getQuery()
                  ->getResult();
    }
    
    
}
