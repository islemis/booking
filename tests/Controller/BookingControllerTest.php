<?php

namespace App\Tests\Controller;

use App\Entity\Booking;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class BookingControllerTest extends WebTestCase{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/booking/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Booking::class);

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
        self::assertPageTitleContains('Booking index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'booking[startDate]' => 'Testing',
            'booking[endDate]' => 'Testing',
            'booking[status]' => 'Testing',
            'booking[userId]' => 'Testing',
            'booking[listingId]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Booking();
        $fixture->setStartDate('My Title');
        $fixture->setEndDate('My Title');
        $fixture->setStatus('My Title');
        $fixture->setUserId('My Title');
        $fixture->setListingId('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Booking');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Booking();
        $fixture->setStartDate('Value');
        $fixture->setEndDate('Value');
        $fixture->setStatus('Value');
        $fixture->setUserId('Value');
        $fixture->setListingId('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'booking[startDate]' => 'Something New',
            'booking[endDate]' => 'Something New',
            'booking[status]' => 'Something New',
            'booking[userId]' => 'Something New',
            'booking[listingId]' => 'Something New',
        ]);

        self::assertResponseRedirects('/booking/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getStartDate());
        self::assertSame('Something New', $fixture[0]->getEndDate());
        self::assertSame('Something New', $fixture[0]->getStatus());
        self::assertSame('Something New', $fixture[0]->getUserId());
        self::assertSame('Something New', $fixture[0]->getListingId());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Booking();
        $fixture->setStartDate('Value');
        $fixture->setEndDate('Value');
        $fixture->setStatus('Value');
        $fixture->setUserId('Value');
        $fixture->setListingId('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/booking/');
        self::assertSame(0, $this->repository->count([]));
    }
}
