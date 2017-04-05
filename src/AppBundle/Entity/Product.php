<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table(name="tblProductData")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductRepository")
 */
class Product
{
    /**
     * @var int
     *
     * @ORM\Column(name="productDataId", type="integer", options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="productCode", type="string", length=10, unique=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="productName", type="string", length=50)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="productDesc", type="string", length=255)
     */
    private $description;

    /**
     * @var
     *
     * @ORM\Column(name="dtmAdded", type="datetime", nullable=true)
     */
    private $addDate;

    /**
     * @var float
     *
     * @ORM\Column(name="productPrice", type="float")
     */
    private $price;

    /**
     * @var integer
     *
     * @ORM\Column(name="productStock", type="integer")
     */
    private $stock;

    /**
     * @var integer
     *
     * @ORM\Column(name="productQuantity", type="integer", nullable=true)
     */
    private $quantity;

    /**
     * @var
     *
     * @ORM\Column(name="dtmDiscontinued", type="datetime", nullable=true)
     */
    private $discontinuedDate;

    /**
     * @var
     *
     * @ORM\Column(name="stmTimestamp", type="datetime", nullable=false)
     * @ORM\Version
     */
    private $currentTimestamp;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getAddDate()
    {
        return $this->addDate;
    }

    /**
     * @param mixed $addDate
     */
    public function setAddDate($addDate)
    {
        $this->addDate = $addDate;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return int
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * @param int $stock
     */
    public function setStock($stock)
    {
        $this->stock = $stock;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return mixed
     */
    public function getDiscontinuedDate()
    {
        return $this->discontinuedDate;
    }

    /**
     * @param mixed $discontinuedDate
     */
    public function setDiscontinuedDate($discontinuedDate)
    {
        $this->discontinuedDate = $discontinuedDate;
    }
}

