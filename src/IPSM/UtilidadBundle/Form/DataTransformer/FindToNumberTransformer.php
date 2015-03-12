<?php
/**
 * Created by PhpStorm.
 * User: cacho
 * Date: 3/09/14
 * Time: 10:37
 */

namespace IPSM\UtilidadBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;


class FindToNumberTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * nombre de la clase
     */
    private $className;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om, $className)
    {
        $this->className = $className;
        $this->om = $om;
    }

    /**
     * Transforma un objeto (``obj``) a una cadena (``number``).
     *
     * @param  Obj|null $issue
     * @return string
     */
    public function transform($obj)
    {
        if (null === $obj) {
            return "";
        }

        if(is_numeric($obj) && $this->className == null)
            return $obj;

        return $obj->getId();
    }

    /**
     * Transforma una cadena (``number``) a un objeto (``issue``).
     *
     * @param  string $number
     *
     * @return Empleado|null
     *
     * @throws TransformationFailedException si no encuentra el objeto (issue).
     */
    public function reverseTransform($number)
    {
        if (!$number) {
            return null;
        }

        if($this->className == null ){
            return $number;
        }

        $obj = $this->om
            ->getRepository($this->className)
            ->findOneBy(array('id' => $number))
        ;

        if (null === $obj) {
            throw new TransformationFailedException(sprintf(
                'el dato "%s" no existe!',
                $number
            ));
        }

        return $obj;
    }
}