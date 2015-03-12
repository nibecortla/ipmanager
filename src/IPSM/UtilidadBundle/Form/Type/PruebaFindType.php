<?php

/**
 * Created by PhpStorm.
 * User: cacho
 * Date: 17/03/14
 * Time: 18:13
 */

namespace IPSM\UtilidadBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;



class PruebaFindType  extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Persona','find',
                array(
                    'form_type'     => __CLASS__,
                    'titulo'        => 'Busqueda de Persona',
                    'entity_class'  => "DejosBundle:Empleado",
                    'columns'       => array("id","documento","nombre"),
                    'labels'        => array("Id","Documento","Apellido y Nombre"),
                    'sort_by'       => 'nombre',
                    'col_id'        => "id",

                )
            )
            ->add('Prueba')
            ->add('Prueba2')

        ;
    }


    public function getName()
    {
        return "prueba_find";
    }
} 