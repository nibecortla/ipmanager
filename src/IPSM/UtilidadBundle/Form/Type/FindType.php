<?php
/**
 * Created by PhpStorm.
 * User: cacho
 * Date: 16/03/14
 * Time: 12:11
 */

namespace IPSM\UtilidadBundle\Form\Type;


use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManager;
use IPSM\UtilidadBundle\Form\DataTransformer\FindToNumberTransformer;

use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\RuntimeException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;


class FindType extends AbstractType
{

    /**
     * @var ManagerRegistry
     */
    protected $container;

    protected $inicio;

    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $este = $this;

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($este) {
                //El data es el objetoasociado a ese elemento
                $data = $event->getData();
                $form = $event->getForm();

                $ops = $form->getConfig()->getOptions();
                if ($data) {
                    //if()
                    $view = $este->container->get('templating')->render(
                        $ops['twig_return'],
                        array(
                            'dato' => $data,
                            'campos' => $ops['columns'],
                            'labels' => $ops['labels'],
                        )
                    );

                    $este->setInicio($view);
                }

            }
        );

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
            }
        );
        $em = $options['em'];

        $transformer = new FindToNumberTransformer($em, $builder->getOption('entity_class'));
        $builder->addModelTransformer($transformer);

    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {

        $vars = array();
        $vars['form_type'] = str_replace('\\', "|", $options['form_type']);
        $vars['form_colname'] = $form->getName();
        $vars['form_control_class'] = $options['form_control_class'];
        $vars['btn_find_img'] = $options['btn_find_img'];
        $vars['show_inicio'] = $this->inicio;
        $view->vars = array_merge($view->vars, $vars);

        $view->vars['vars'] = "";
    }

    /**
     * {@inheritdoc}
     */
//    public function finishView(FormView $view, FormInterface $form, array $options)
//    {
//    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $registry = $this->container->get("doctrine");

        $emNormalizer = function (Options $options, $em) use ($registry) {
            /* @var ManagerRegistry $registry */
            if (null !== $em) {
                return $registry->getManager($em);
            }

            $em = $registry->getManagerForClass($options['entity_class']);

            if (null === $em) {
                throw new RuntimeException(sprintf(
                    'Class "%s" seems not to be a managed Doctrine entity. ' .
                    'Did you forget to map it?',
                    $options['entity_class']
                ));
            }

            return $em;
        };

        $colidNormalizer = function (Options $options, $col_id) {
            if (null == $col_id) {
                throw new RuntimeException(sprintf(
                    'El valor de "col_id" es obligatorio'
                ));
            }

            if (!in_array($col_id, $options['columns'])) {
                throw new RuntimeException(sprintf(
                    'El valor de col_id "%s" no esta dentro de las columnas "columns". ',
                    $col_id
                ));
            }

            return $col_id;
        };

        $type = $this;
        $loader = function (Options $options) use ($type) {
            if (null !== $options['query_builder']) {
                return $type->getLoader($options['em'], $options['query_builder'], $options['entity_class']);
            }
        };

        $resolver->setRequired(
            array(
                'form_type',
                'form_colname',
                'entity_class',
                'columns',
                'col_id',
                'twig_list',
                'twig_return',
            )
        );

        $resolver->setDefaults(
            array(
                //'form_type'         => null,
                'form_colname' => null,
                'em' => null,
                'form_control_class' => 'form-control-find',
                // estilo visual del control en el form
                'titulo' => "Busqueda...",
                // Titulo del pop up de la búsqueda
                'entity_class' => "",
                // Modelo de la clase
                'query_builder' => null,
                // un query builder o un clousere que recibe un EntityRepository como parametro
                'loader' => $loader,
                // sera el encargado si se define un query builder.
                'col_id' => null,
                // Columna con el cual se actualizara el campo en cuestion,
                // por lo gral será el id de la tabla a consultar.
                'columns' => array(),
                // Columnas con la cual se armará la grilla
                'labels' => array(),
                // Labels de las columnas, si no se espesifica es el nombre de las columnas
                'sort_by' => null,
                // para de valores por cual se ordenaran los resultados
                'twig_list' => "UtilidadBundle:Find:lista.html.twig",
                // el twig que se utilizara para renderear la lista con los datos
                'twig_return' => 'UtilidadBundle:Find:find_return.html.twig',
                'btn_find_img' => // Imagen del boton de busqueda predeterminado
                    'bundles/utilidad/images/search-button-blue.png',
                'show_inicio' => "hola",
            )
        );

        $resolver->setNormalizers(
            array(
                'em' => $emNormalizer,
                'col_id' => $colidNormalizer,
                'loader' => $loader

            )
        );
    }

    public function getLoader(EntityManager $manager, $queryBuilder, $class)
    {

        if (!($queryBuilder instanceof QueryBuilder || $queryBuilder instanceof \Closure)) {
            throw new UnexpectedTypeException($queryBuilder, 'Doctrine\ORM\QueryBuilder or \Closure');
        }


        if ($queryBuilder instanceof \Closure) {

            if (!$manager instanceof EntityManager) {
                throw new UnexpectedTypeException($manager, 'Doctrine\ORM\EntityManager');
            }

            $queryBuilder = $queryBuilder($manager->getRepository($class));

            if (!$queryBuilder instanceof QueryBuilder) {
                throw new UnexpectedTypeException($queryBuilder, 'Doctrine\ORM\QueryBuilder');
            }
        }

        return $queryBuilder;

    }


    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return "find";
    }


    public function setInicio($ini)
    {
        $this->inicio = $ini;
    }

    public function getInicio()
    {
        return $this->inicio;
    }

} 