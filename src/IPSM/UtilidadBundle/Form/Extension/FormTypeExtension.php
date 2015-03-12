<?php
/**
 * Created by PhpStorm.
 * User: cacho
 * Date: 17/03/14
 * Time: 11:39
 */

namespace  IPSM\UtilidadBundle\Form\Extension;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class FormTypeExtension extends AbstractTypeExtension
{

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['form_control_class'] = $options['form_control_class'];

    }
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'form_control_class' => 'form-control',
        ));

    }
    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'form';
    }
}