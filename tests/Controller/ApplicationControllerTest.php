<?php

namespace App\Tests\Controller;

use App\Entity\Application;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ApplicationControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $applicationRepository;
    private string $path = '/application/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->applicationRepository = $this->manager->getRepository(Application::class);

        foreach ($this->applicationRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Application index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'application[status]' => 'Testing',
            'application[message]' => 'Testing',
            'application[submittedAt]' => 'Testing',
            'application[cvSnapshotPath]' => 'Testing',
            'application[candidat]' => 'Testing',
            'application[jobOffer]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->applicationRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Application();
        $fixture->setStatus('My Title');
        $fixture->setMessage('My Title');
        $fixture->setSubmittedAt('My Title');
        $fixture->setCvSnapshotPath('My Title');
        $fixture->setCandidat('My Title');
        $fixture->setJobOffer('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Application');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Application();
        $fixture->setStatus('Value');
        $fixture->setMessage('Value');
        $fixture->setSubmittedAt('Value');
        $fixture->setCvSnapshotPath('Value');
        $fixture->setCandidat('Value');
        $fixture->setJobOffer('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'application[status]' => 'Something New',
            'application[message]' => 'Something New',
            'application[submittedAt]' => 'Something New',
            'application[cvSnapshotPath]' => 'Something New',
            'application[candidat]' => 'Something New',
            'application[jobOffer]' => 'Something New',
        ]);

        self::assertResponseRedirects('/application/');

        $fixture = $this->applicationRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getStatus());
        self::assertSame('Something New', $fixture[0]->getMessage());
        self::assertSame('Something New', $fixture[0]->getSubmittedAt());
        self::assertSame('Something New', $fixture[0]->getCvSnapshotPath());
        self::assertSame('Something New', $fixture[0]->getCandidat());
        self::assertSame('Something New', $fixture[0]->getJobOffer());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Application();
        $fixture->setStatus('Value');
        $fixture->setMessage('Value');
        $fixture->setSubmittedAt('Value');
        $fixture->setCvSnapshotPath('Value');
        $fixture->setCandidat('Value');
        $fixture->setJobOffer('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/application/');
        self::assertSame(0, $this->applicationRepository->count([]));
    }
}
