<?php

namespace App\Tests\Controller;

use App\Entity\Candidat;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CandidatControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $candidatRepository;
    private string $path = '/candidat/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->candidatRepository = $this->manager->getRepository(Candidat::class);

        foreach ($this->candidatRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Candidat index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'candidat[firstName]' => 'Testing',
            'candidat[lastName]' => 'Testing',
            'candidat[email]' => 'Testing',
            'candidat[phone]' => 'Testing',
            'candidat[address]' => 'Testing',
            'candidat[dateOfBirth]' => 'Testing',
            'candidat[resumePath]' => 'Testing',
            'candidat[coverLetterPath]' => 'Testing',
            'candidat[linkedinUrl]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->candidatRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Candidat();
        $fixture->setFirstName('My Title');
        $fixture->setLastName('My Title');
        $fixture->setEmail('My Title');
        $fixture->setPhone('My Title');
        $fixture->setAddress('My Title');
        $fixture->setDateOfBirth('My Title');
        $fixture->setResumePath('My Title');
        $fixture->setCoverLetterPath('My Title');
        $fixture->setLinkedinUrl('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Candidat');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Candidat();
        $fixture->setFirstName('Value');
        $fixture->setLastName('Value');
        $fixture->setEmail('Value');
        $fixture->setPhone('Value');
        $fixture->setAddress('Value');
        $fixture->setDateOfBirth('Value');
        $fixture->setResumePath('Value');
        $fixture->setCoverLetterPath('Value');
        $fixture->setLinkedinUrl('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'candidat[firstName]' => 'Something New',
            'candidat[lastName]' => 'Something New',
            'candidat[email]' => 'Something New',
            'candidat[phone]' => 'Something New',
            'candidat[address]' => 'Something New',
            'candidat[dateOfBirth]' => 'Something New',
            'candidat[resumePath]' => 'Something New',
            'candidat[coverLetterPath]' => 'Something New',
            'candidat[linkedinUrl]' => 'Something New',
        ]);

        self::assertResponseRedirects('/candidat/');

        $fixture = $this->candidatRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getFirstName());
        self::assertSame('Something New', $fixture[0]->getLastName());
        self::assertSame('Something New', $fixture[0]->getEmail());
        self::assertSame('Something New', $fixture[0]->getPhone());
        self::assertSame('Something New', $fixture[0]->getAddress());
        self::assertSame('Something New', $fixture[0]->getDateOfBirth());
        self::assertSame('Something New', $fixture[0]->getResumePath());
        self::assertSame('Something New', $fixture[0]->getCoverLetterPath());
        self::assertSame('Something New', $fixture[0]->getLinkedinUrl());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Candidat();
        $fixture->setFirstName('Value');
        $fixture->setLastName('Value');
        $fixture->setEmail('Value');
        $fixture->setPhone('Value');
        $fixture->setAddress('Value');
        $fixture->setDateOfBirth('Value');
        $fixture->setResumePath('Value');
        $fixture->setCoverLetterPath('Value');
        $fixture->setLinkedinUrl('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/candidat/');
        self::assertSame(0, $this->candidatRepository->count([]));
    }
}
