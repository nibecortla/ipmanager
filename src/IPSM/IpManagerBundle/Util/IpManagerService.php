<?php
/**
 * Created by PhpStorm.
 * User: cachorios
 * Date: 12/06/14
 * Time: 20:36
 */

namespace IPSM\IpManagerBundle\Util;


use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\Container;

class IpManagerService {
    private $liquidacion;

    private $contenedor;

    public function __construct(Container $contenedor)
    {
        $this->contenedor = $contenedor;
    }

    /**
     * @return \IPSM\IpManagerBundle\Entity\Persona
     */
    public function getPersona()
    {

        if($this->contenedor->get("security.context")->getToken()->isAuthenticated()) {
            $usuario = $this->contenedor->get("security.context")->getToken()->getUser();
            return $usuario->getPersona();
        }

        return null;
    }

//    public function getLiquidacionActiva(){
//        if($this->liquidacion == null){
//            $liq = $this->contenedor->get("session")->get("liquidacion.activa",0);
//            $em = $this->contenedor->get("doctrine.orm.entity_manager");
//
//            $this->liquidacion = $em->getRepository("DejosBundle:Liquidacion")->find($liq);
//        }
//        return $this->liquidacion;
//    }
//
//    public function getUsuario(){
//       return $this->contenedor->get("security.context")->getToken()->getUser();
//    }




} 