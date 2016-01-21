<?php
namespace Sdz\BlogBundle\Beta;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class BetaListener
{
// La date de fin de la version beta :
// -Avant cette date, on affichera un compte à rebours (J-3 par exemple)
// -Après cette date, on n'affichera plus le <<beta>>

    protected $dateFin;
    
    public function __construct($dateFin)
    {
        $this->dateFin = new \DateTime($dateFin);
    }
    
    
    // Méthode pour ajouter le <<beta>> à une réponse
    
    protected function displayBeta(Response $reponse, $joursRestant)
    {
      
      $content =   $reponse->getContent();
      
      // code à rajouter
      
      $html = '<span style="color: red; font-size: 0.5em;"> -Beta J-'.(int) $joursRestant.' !<span>';
      
      // Insertion du code dans la page, dans le <h1> du header
      $content =  preg_replace('#<h1>(.*?)</h1>#iU',
                               '<h1>$1'.$html.'</h1>',
                               $content,
                               1);
      
      // Modification du contenun dans la reponse
      $reponse->setContent($content);
      
      return $reponse;
    }
    
    public function onKernelResponse(FilterResponseEvent $event)
    {
        // On teste si la requete est bien la requet principale
        if(HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType())
        {
            return;
        }
        
        // On recupere la réponse que le noyau a inseré  dans l'evenement
        $response = $event->getResponse();
        
        // Ici on modifie comme on veut la réponse
        
        $joursRestant = $this->dateFin->diff(new \DateTime())->days;
        
        if ($joursRestant > 0 ) {
            // On utilise notre méthode <<reine>>
            $response = $this->displayBeta($event->getResponse(), $joursRestant );
        }
        
        // Puis on insère la réponse modifié dans l'évènement
        
        $event->setResponse($response);
        
    }
    

}
