<?php

namespace Sdz\BlogBundle\Bigbrother;
use Symfony\Component\Security\Core\User\UserInterface;

class CensureListener
{
    // Liste des id des utilisateurs a surveiller
    protected $liste;
    protected $mailer;
    
    public function __construct(array $liste, \Swift_Mailer $mailer)
    {
        $this->liste    = $liste;
        $this->mailer   = $mailer;
    }
    
    // Méthode <<reine>> 1
    
    protected function sendEmail($message, UserInterface $user)
    {
        $message = \Swift_Message::newinstance()
             ->setSubject("Nouveau message d'un utilisateur surveillé ")
             ->setForm('ntyoussouf@gmail.com')
             ->setTo('ntyoussouf@gmail.com')
             ->setBody("L'utilisateur surveillé '".$user->getUsername()."'a posté le message suivant : '".$message."'");
             
             $this->mailer->send($message);
    }
    
    // Methode <<reine>> 2
    protected function censureMessage($message)
    {
        // Ici , totalement arbitraire :
        
        $message = str_replace(array('top secret', 'mot interdit'), '', $message);
        
        return $message;
        
    }
    
    // Méthode <<technique>> de liaison entre l'évenement et la fonctionnalité reine
    
    public function onMessagePost(MessagePostEvent $event)
    {
        // On active la surveillance si l'auteur du message est dans la liste
        if (in_array($event->getUser()->getId(), $this->liste))
        {
            // On envoie un email a l'administrateur
            $this->sendEmail($event->getMessage(), $event->getUser());
            
            // on censure le message
            $message = $this->censureMessage($event->getMessage());
            //On enregistre le message censuré dans l'event
            $event->setMessage($message);
        }
    }
    
    
    
}
