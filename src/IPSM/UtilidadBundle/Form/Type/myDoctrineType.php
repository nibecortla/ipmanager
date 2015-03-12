<?php

/*
 * This file is part of the Symfony package.
 * changed by Lar
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace  IPSM\UtilidadBundle\Form\Type;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Form\Exception\RuntimeException;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\Form\ChoiceList\ORMQueryBuilderLoader;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityLoaderInterface;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;




abstract class myDoctrineType extends AbstractType
{
    /**
     * @var ManagerRegistry
     */
    protected $registry;

    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    public function __construct(ManagerRegistry $registry, PropertyAccessorInterface $propertyAccessor = null)
    {
        $this->registry = $registry;
        $this->propertyAccessor = $propertyAccessor ?: PropertyAccess::createPropertyAccessor();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        if ($options['multiple']) {
//            $builder
//                ->addEventSubscriber(new MergeDoctrineCollectionListener())
//                ->addViewTransformer(new CollectionToArrayTransformer(), true)
//            ;
//        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

        $registry = $this->registry;
        $propertyAccessor = $this->propertyAccessor;
        $type = $this;

        $loader = function (Options $options) use ($type) {
            if (null !== $options['query_builder']) {
                return $type->getLoader($options['em'], $options['query_builder'], $options['class']);
            }

            return null;
        };


        $emNormalizer = function (Options $options, $em) use ($registry) {
            /* @var ManagerRegistry $registry */
            if (null !== $em) {
                return $registry->getManager($em);
            }

            $em = $registry->getManagerForClass($options['class']);

            if (null === $em) {
                throw new RuntimeException(sprintf(
                    'Class "%s" seems not to be a managed Doctrine entity. ' .
                    'Did you forget to map it?',
                    $options['class']
                ));
            }

            return $em;
        };

        $resolver->setDefaults(array(
            'em'                => null,
            'property'          => null,
            'query_builder'     => null,
            'loader'            => $loader,
            'choices'           => null,
            'group_by'          => null,
        ));

        $resolver->setRequired(array('class'));

        $resolver->setNormalizers(array(
            'em' => $emNormalizer,
        ));

        $resolver->setAllowedTypes(array(
            'loader' => array('null', 'Symfony\Bridge\Doctrine\Form\ChoiceList\EntityLoaderInterface'),
        ));
    }

    /*******
     * Return the default loader object.
     *
     * @param ObjectManager $manager
     * @param mixed         $queryBuilder
     * @param string        $class
     *
     * @return EntityLoaderInterface
     */
    //abstract public function getLoader(ObjectManager $manager, $queryBuilder, $class);

    public function getLoader(ObjectManager $manager, $queryBuilder, $class)
    {
        return new ORMQueryBuilderLoader(
            $queryBuilder,
            $manager,
            $class
        );
    }

    public function getParent()
    {
        return 'text';
    }

}




