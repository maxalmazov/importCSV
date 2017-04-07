<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\Product;
use AppBundle\Services\ValidatorCSV;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

class ConvertCSVTest extends TestCase
{
    public function testValidateStock()
    {
        $employee = $this->createMock(Product::class);

        $employeeRepository = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $employeeRepository ->expects($this->any())
            ->method('__call')
            ->with('findOneBy')
            ->will($this->returnValue($employee));

        $entityManager = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($employeeRepository));

        $stock = new ValidatorCSV($entityManager);
        $result = $stock->validate(array(
            'Cost in GBP' => 10,
            'Stock' => 11,
            'Product Name' => 5,
            'Product Code' => 'P1458',
            'Product Description' => 'Some text',
            'Discontinued' => '',
        ));

        $this->assertInstanceOf('AppBundle\Entity\Product', $result);
    }
}
