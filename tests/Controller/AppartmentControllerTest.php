<?php

namespace App\Tests\Controller;

use App\Entity\Appartment;
use App\Repository\AppartmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class AppartmentControllerTest extends WebTestCase{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/appartment/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Appartment::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Appartment index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'appartment[type]' => 'Testing',
            'appartment[numberOfRooms]' => 'Testing',
            'appartment[squareMeters]' => 'Testing',
            'appartment[furnished]' => 'Testing',
            'appartment[listingId]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Appartment();
        $fixture->setType('My Title');
        $fixture->setNumberOfRooms('My Title');
        $fixture->setSquareMeters('My Title');
        $fixture->setFurnished('My Title');
        $fixture->setListingId('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Appartment');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Appartment();
        $fixture->setType('Value');
        $fixture->setNumberOfRooms('Value');
        $fixture->setSquareMeters('Value');
        $fixture->setFurnished('Value');
        $fixture->setListingId('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'appartment[type]' => 'Something New',
            'appartment[numberOfRooms]' => 'Something New',
            'appartment[squareMeters]' => 'Something New',
            'appartment[furnished]' => 'Something New',
            'appartment[listingId]' => 'Something New',
        ]);

        self::assertResponseRedirects('/appartment/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getType());
        self::assertSame('Something New', $fixture[0]->getNumberOfRooms());
        self::assertSame('Something New', $fixture[0]->getSquareMeters());
        self::assertSame('Something New', $fixture[0]->getFurnished());
        self::assertSame('Something New', $fixture[0]->getListingId());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Appartment();
        $fixture->setType('Value');
        $fixture->setNumberOfRooms('Value');
        $fixture->setSquareMeters('Value');
        $fixture->setFurnished('Value');
        $fixture->setListingId('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/appartment/');
        self::assertSame(0, $this->repository->count([]));
    }
}
