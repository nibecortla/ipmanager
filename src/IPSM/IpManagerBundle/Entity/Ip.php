<?php

namespace IPSM\IpManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * Ip
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="IPSM\IpManagerBundle\Entity\IpRepository")
 */
class Ip
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=255)
     */
    private $ip;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255)
     */
    private $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="observacion", type="string", length=255)
     */
    private $observacion;

    /**
     * @ORM\ManyToOne(targetEntity="Persona")
     */
    protected $persona;

    /**
     * @ORM\ManyToOne(targetEntity="Grupo")
     */
    protected $grupo;

    /**
     * @ORM\ManyToOne(targetEntity="Area")
     */
    protected $area;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return Ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Ip
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set observacion
     *
     * @param string $observacion
     * @return Ip
     */
    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;

        return $this;
    }

    /**
     * Get observacion
     *
     * @return string 
     */
    public function getObservacion()
    {
        return $this->observacion;
    }

    /**
     * Set persona
     *
     * @param \IPSM\IpManagerBundle\Entity\Persona $persona
     * @return Ip
     */
    public function setPersona(\IPSM\IpManagerBundle\Entity\Persona $persona = null)
    {
        $this->persona = $persona;

        return $this;
    }

    /**
     * Get persona
     *
     * @return \IPSM\IpManagerBundle\Entity\Persona 
     */
    public function getPersona()
    {
        return $this->persona;
    }

    /**
     * Set grupo
     *
     * @param \IPSM\IpManagerBundle\Entity\Grupo $grupo
     * @return Ip
     */
    public function setGrupo(\IPSM\IpManagerBundle\Entity\Grupo $grupo = null)
    {
        $this->grupo = $grupo;

        return $this;
    }

    /**
     * Get grupo
     *
     * @return \IPSM\IpManagerBundle\Entity\Grupo 
     */
    public function getGrupo()
    {
        return $this->grupo;
    }

    /**
     * Set area
     *
     * @param \IPSM\IpManagerBundle\Entity\Area $area
     * @return Ip
     */
    public function setArea(\IPSM\IpManagerBundle\Entity\Area $area = null)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area
     *
     * @return \IPSM\IpManagerBundle\Entity\Area 
     */
    public function getArea()
    {
        return $this->area;
    }
}
