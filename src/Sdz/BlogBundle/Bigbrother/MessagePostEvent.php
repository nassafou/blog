<?php

namespace Sdz\BlogBundle\Bigbrother;
use Symfony\Component\EventDispatcher\Event;


class MessagePostEvent extends Event
{
    #attributs
    protected $message;
    protected $user;
    protected $autorise;
    
    
    public function __construct($message, UserInterface $user)
    {
        $this->message   = $message;
        $this->user      = $user;
    }
    
    // le listener doit avoir acces au message
    public function getMessage()
    {
        return $this->message;
    }
    
    // le listener doit pouvoir modifier le message
    public function setMessage()
    {
        return $this->message = $message;
    }
    
    // le listener doit avoir acces a l'utilisateur
    public function getUser()
    {
        return $this->user;
    }
    
    // Pas de setUser, le listener ne peut pas modifier l'auteur du message
    
    
    
    
}
