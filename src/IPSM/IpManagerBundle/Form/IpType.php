<?php

namespace IPSM\IpManagerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class IpType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ip')
            ->add('descripcion')
            ->add('observacion')
            ->add('persona')
            ->add('grupo')
            ->add('area')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'IPSM\IpManagerBundle\Entity\Ip'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ipsm_ipmanagerbundle_ip';
    }
}
