<?php

namespace AppBundle\Services;

use AppBundle\Entity\Product;
use Doctrine\ORM\EntityManager;

class ValidatorCSV
{
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function validate($row)
    {
        if ($this->validatePrice($row) && $this->validateStok($row) && $this->isProductExist($row)) {
            $product = new Product();

            $product->setCode($row['Product Code']);
            $product->setName($row['Product Name']);
            $product->setDescription($row['Product Description']);
            $product->setPrice($row['Cost in GBP']);
            $product->setStock($row['Stock']);
            $product->setAddDate(new \DateTime('now'));

            if ($this->isDiscontinued($row)) {
                $product->setDiscontinuedDate(new \DateTime('now'));
            } else {
                $product->setDiscontinuedDate(null);
            }

            if ($this->isExistQuantity($row)) {
                $product->setQuantity($row['Quantity']);
            } else {
                $product->setQuantity(null);
            }

            return $product;
        } else {
            $invalidLine = $row['Product Code'];

            return $invalidLine;
        }
    }

    private function validatePrice($row)
    {
        $priceInUSD = (int)$row['Cost in GBP']*1.25;//1.25 - coefficient gbp/usd

        if ($priceInUSD > 10 && $priceInUSD < 1000) {
            return true;
        }
        return false;
    }


    private function validateStok($row)
    {
        if ((int)$row['Stock'] > 10) {
            return true;
        }
        return false;
    }

    private function isProductExist ($row)
    {
        $product = $this->em->getRepository('AppBundle:Product')
            ->findOneBy(array('code' => $row['Product Code']));

        if (!(is_object($product))) {
            return true;
        } else {
            return false;
        }
    }

    private function isDiscontinued($row)
    {
        if ($row['Discontinued'] === 'yes') {
            return true;
        }
        return false;
    }

    private function isExistQuantity($row)
    {
        if (array_key_exists('Quantity', $row)) {
            return true;
        }
        return false;
    }
}