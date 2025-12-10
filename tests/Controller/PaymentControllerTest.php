<?php

namespace App\Tests\Controller;

use App\Entity\Payment;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class PaymentControllerTest extends WebTestCase{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/payment/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Payment::class);

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
        self::assertPageTitleContains('Payment index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'payment[amount]' => 'Testing',
            'payment[paymentDate]' => 'Testing',
            'payment[paymentStatus]' => 'Testing',
            'payment[bookingId]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Payment();
        $fixture->setAmount('My Title');
        $fixture->setPaymentDate('My Title');
        $fixture->setPaymentStatus('My Title');
        $fixture->setBookingId('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Payment');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Payment();
        $fixture->setAmount('Value');
        $fixture->setPaymentDate('Value');
        $fixture->setPaymentStatus('Value');
        $fixture->setBookingId('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'payment[amount]' => 'Something New',
            'payment[paymentDate]' => 'Something New',
            'payment[paymentStatus]' => 'Something New',
            'payment[bookingId]' => 'Something New',
        ]);

        self::assertResponseRedirects('/payment/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getAmount());
        self::assertSame('Something New', $fixture[0]->getPaymentDate());
        self::assertSame('Something New', $fixture[0]->getPaymentStatus());
        self::assertSame('Something New', $fixture[0]->getBookingId());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Payment();
        $fixture->setAmount('Value');
        $fixture->setPaymentDate('Value');
        $fixture->setPaymentStatus('Value');
        $fixture->setBookingId('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/payment/');
        self::assertSame(0, $this->repository->count([]));
    }
}
