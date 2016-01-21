<?php

namespace Sdz\BlogBundle\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class TestSubscriber implements EventSubscriberInterface
{
    // la methode de l'interface que l'on doit implementé, à définir en static
    
    static public function getSubscribedEvents()
    {
        // On retoune un tableau << nom de l'évenement >> => << Méthode à executer >>
        
        return array(
                     'kernel.response' => 'onkernelResponse',
                     'store.order'     => 'onStoreOrder'
                     );
    }
    
    public function onkernelResponse(FilterResponseEvent $event)
    {
        //
    }
    
    public function onStoreOrder(FilterOrderEvent $event )
    {
        //
    }
    
}

