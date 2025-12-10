<?php

namespace App\Tests\Controller;

use App\Entity\Listing;
use App\Repository\ListingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ListingControllerTest extends WebTestCase{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/listing/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Listing::class);

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
        self::assertPageTitleContains('Listing index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'listing[title]' => 'Testing',
            'listing[description]' => 'Testing',
            'listing[address]' => 'Testing',
            'listing[rentPrice]' => 'Testing',
            'listing[availabefrom]' => 'Testing',
            'listing[owner]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Listing();
        $fixture->setTitle('My Title');
        $fixture->setDescription('My Title');
        $fixture->setAddress('My Title');
        $fixture->setRentPrice('My Title');
        $fixture->setAvailabefrom('My Title');
        $fixture->setOwner('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Listing');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Listing();
        $fixture->setTitle('Value');
        $fixture->setDescription('Value');
        $fixture->setAddress('Value');
        $fixture->setRentPrice('Value');
        $fixture->setAvailabefrom('Value');
        $fixture->setOwner('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'listing[title]' => 'Something New',
            'listing[description]' => 'Something New',
            'listing[address]' => 'Something New',
            'listing[rentPrice]' => 'Something New',
            'listing[availabefrom]' => 'Something New',
            'listing[owner]' => 'Something New',
        ]);

        self::assertResponseRedirects('/listing/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitle());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getAddress());
        self::assertSame('Something New', $fixture[0]->getRentPrice());
        self::assertSame('Something New', $fixture[0]->getAvailabefrom());
        self::assertSame('Something New', $fixture[0]->getOwner());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Listing();
        $fixture->setTitle('Value');
        $fixture->setDescription('Value');
        $fixture->setAddress('Value');
        $fixture->setRentPrice('Value');
        $fixture->setAvailabefrom('Value');
        $fixture->setOwner('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/listing/');
        self::assertSame(0, $this->repository->count([]));
    }
}
