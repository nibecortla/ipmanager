<?php

namespace IPSM\UtilidadBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use IPSM\UtilidadBundle\Form\Type\PruebaFindType;

class DefaultController extends Controller {
    public function indexAction()
    {

        // ladybug_dump_die($res);
        $form = $this->createForm(new PruebaFindType(),array());
//        $f = $form->get('persona_id');


        return $this->render('UtilidadBundle:Default:index.html.twig', array('form' => $form->createView()));
    }
}
