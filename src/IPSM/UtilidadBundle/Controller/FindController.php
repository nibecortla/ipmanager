<?php
/**
 * Created by PhpStorm.
 * User: cacho
 * Date: 25/03/14
 * Time: 19:07
 */

namespace IPSM\UtilidadBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use IPSM\UtilidadBundle\Form\Type\FindTypeInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class FindController extends Controller
{
    public function listaAction(Request $request)
    {
        $toFind = trim($request->get("txtfind", ""));
        $claseType = $request->get("form_type", "");
        $ByIni = $request->get("ByIni", "0");


        if ($request->getMethod() == 'POST') {
            $get2url = "?txtfind=" . $toFind;
            $get2url .= "&form_type=" . $claseType;
            $get2url .= "&form_colname=" . $request->get("form_colname", "");
            $get2url .= "&ByIni=1";

            return $this->redirect(
                $request->getRequestUri() . $get2url
            );
        }


        //Url para el text find
        $url = $this->generateUrl("utilidades_find"); //.$url;


        if (!$toFind || strlen($toFind) < 2) {
            $reponse = new Response("Ingrese dato de busqueda", 409);

            return $reponse;
        }


        $claseTypeLimpio = str_replace("|", '\\', $claseType);

        $fType = new $claseTypeLimpio();

        if( $fType instanceof FindTypeInterface ){
            $fType->setContainer($this->container);
        }

        $form = $this->createForm($fType);


        $opciones = $form->get($request->get("form_colname", ""))->getConfig();

        $titulo = $opciones->getOption("titulo");
        $entity = $opciones->getOption("entity_class");
        $columns = $opciones->getOption("columns");
        $labels = $opciones->getOption("labels");
        $col_id = $opciones->getOption("col_id");
        $sort_by = $opciones->getOption("sort_by");
        $twig_list = $opciones->getOption("twig_list");
        $twig_return = $opciones->getOption("twig_return");

        $loader = $opciones->getOption("loader");

        if ($loader) {
            $queryBuilder = $loader;
        } else {
            $queryBuilder = $this->getDoctrine()->getRepository($entity)->createQueryBuilder('p');
        }

        if ($sort_by) {
            $queryBuilder->addOrderBy('p.' . $sort_by);
        }
        $pager = $this->createFilter($queryBuilder, $columns, $toFind, $entity);

        if (count($labels) < count($columns)) {
            $labels = $columns;
            foreach ($labels as $i => $label) {
                $labels[$i] = ucwords($label);
            }
        }

        $queryBuilder->setMaxResults(10);

        $result = $queryBuilder->getQuery()->getResult();

        if (count($result) == 1 && $ByIni == 1) {

            $ret = array();
            $ret['id'] = call_user_func(array($result[0], 'get' . ucfirst($col_id)));
            $ret['html_ret'] = $this->renderView(
                $twig_return,
                array(
                    'dato' => $result[0],
                    'campos' => $columns,
                    'labels' => $labels
                )
            );

            $reponse = new Response(json_encode($ret), 302);

            return $reponse;
        }

        $page = $request->get('page', 1);
        $pager->setMaxPerPage(8);

        try {
            $pager->setCurrentPage($page);
        } catch (NotValidCurrentPageException $e) {
            throw new NotFoundHttpException('Pagina no válida.');
        }

        return $this->render(
            $twig_list,
            array(
                "pager" => $pager,
                'titulo' => $titulo,
                'columns' => $columns,
                'labels' => $labels,
                'textfind' => $toFind,
                'plantilla_return' => $twig_return,
                'col_id' => $col_id,
                'claseType' => $claseType,
                'form_colname' => $request->get("form_colname", ""),
                'urlFind' => $url
            )
        );
    }

    private function str2Array($str)
    {
        return explode(";", $str);
    }

    /**
     * CreaFiltro y retorna una Pagerfanta
     * @param QueryBuilder $queryBuilder
     * @param $columns
     * @param $textFind
     * @param $entity
     * @return Pagerfanta
     */
    private function createFilter(QueryBuilder $queryBuilder, $columns, $textFind, $entity)
    {
        $aliases = $queryBuilder->getRootAliases();
        $alias = $aliases[0];

        $mt = $queryBuilder->getEntityManager()->getClassMetadata($entity);

        $q = array();
        foreach ($columns as $i => $name) {

            //Obtener la definicion metadata del campo
            $field = $mt->getFieldMapping(trim($name));

            if (is_numeric($textFind)) {
                //Si es numerico busco por numerico y por string
                if (in_array($field['type'], array('smallint', 'bigint', 'float', 'integer', 'decimal'))) {
                    $q[] = $alias . '.' . $field['fieldName'] . '= :dato' . $i;
                    $queryBuilder->setParameter('dato' . $i, $textFind);
                } elseif (in_array($field['type'], array('string', 'text'))) {
                    $q[] = $alias . '.' . $field['fieldName'] . ' like :dato' . $i;
                    $queryBuilder->setParameter('dato' . $i, strtoupper($textFind) . '%');
                }
            } else {
                //busco por string
                if (in_array($field['type'], array('string', 'text'))) {
                    $q[] = $alias . '.' . $field['fieldName'] . " like :dato" . $i;
                    $queryBuilder->setParameter('dato' . $i, strtoupper(str_replace(" ", '%', $textFind)) . '%');
                }
            }
        }

        $subq = "";
        foreach ($q as $sq) {
            $subq .= $sq . ' or ';
        }
        if (strlen($subq) > 3) {
            $subq = substr($subq, 0, -4);
            $queryBuilder->andWhere($subq);
        }

        $page = new Pagerfanta(new DoctrineORMAdapter($queryBuilder));

        return $page;
    }

    private function mkFld($fld, $alias)
    {

    }
}