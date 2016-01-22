<?php

namespace Sdz\BlogBundle\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class TestParamConverter implements ParamConverterInterface
{
    protected $class;
    protected $repository;
    
    public function __construct($class, EntityManager $em)
    {
        $this->class          = $class;
        $this->repository     = $em->getRepository($class);
    }
    function support(ConfigurationInterface $configuration)
    {
        // $conf->getClass()  contient la classe de l'argument dans la methode du controleur
        // On test donc si cette classe correspond à notre classe site, contenue dans $this->class
        
        return $configuration->getClass()  == $this->class;
        
    }
    function apply(Request $request, ConfigurationInterface $configuration)
    {
        // On recupère l'entité site correspondante
        $site = $this->repository->findOneByHostname($request->getHost());
        
        // On définit ensuite un attribut de requête du nom de $conf->getName()
        // et contenant notre entité site
        $request->attributes->set($configuration->getName(), $site);
        
        // On retourne true pour qu'un autre paramConverter ne soit utilisé sur cet argument
        //Je pense au notamment au ParaConverter de doctrine qui risque de vouloir s'appliquer
        
        return true;
    }
}

