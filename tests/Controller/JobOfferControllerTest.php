<?php

namespace App\Tests\Controller;

use App\Entity\JobOffer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class JobOfferControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $jobOfferRepository;
    private string $path = '/job/offer/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->jobOfferRepository = $this->manager->getRepository(JobOffer::class);

        foreach ($this->jobOfferRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('JobOffer index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'job_offer[title]' => 'Testing',
            'job_offer[description]' => 'Testing',
            'job_offer[location]' => 'Testing',
            'job_offer[contractType]' => 'Testing',
            'job_offer[salaryMin]' => 'Testing',
            'job_offer[salaryMax]' => 'Testing',
            'job_offer[department]' => 'Testing',
            'job_offer[datetime_immutable]' => 'Testing',
            'job_offer[isActive]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->jobOfferRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new JobOffer();
        $fixture->setTitle('My Title');
        $fixture->setDescription('My Title');
        $fixture->setLocation('My Title');
        $fixture->setContractType('My Title');
        $fixture->setSalaryMin('My Title');
        $fixture->setSalaryMax('My Title');
        $fixture->setDepartment('My Title');
        $fixture->setDatetime_immutable('My Title');
        $fixture->setIsActive('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('JobOffer');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new JobOffer();
        $fixture->setTitle('Value');
        $fixture->setDescription('Value');
        $fixture->setLocation('Value');
        $fixture->setContractType('Value');
        $fixture->setSalaryMin('Value');
        $fixture->setSalaryMax('Value');
        $fixture->setDepartment('Value');
        $fixture->setDatetime_immutable('Value');
        $fixture->setIsActive('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'job_offer[title]' => 'Something New',
            'job_offer[description]' => 'Something New',
            'job_offer[location]' => 'Something New',
            'job_offer[contractType]' => 'Something New',
            'job_offer[salaryMin]' => 'Something New',
            'job_offer[salaryMax]' => 'Something New',
            'job_offer[department]' => 'Something New',
            'job_offer[datetime_immutable]' => 'Something New',
            'job_offer[isActive]' => 'Something New',
        ]);

        self::assertResponseRedirects('/job/offer/');

        $fixture = $this->jobOfferRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitle());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getLocation());
        self::assertSame('Something New', $fixture[0]->getContractType());
        self::assertSame('Something New', $fixture[0]->getSalaryMin());
        self::assertSame('Something New', $fixture[0]->getSalaryMax());
        self::assertSame('Something New', $fixture[0]->getDepartment());
        self::assertSame('Something New', $fixture[0]->getDatetime_immutable());
        self::assertSame('Something New', $fixture[0]->getIsActive());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new JobOffer();
        $fixture->setTitle('Value');
        $fixture->setDescription('Value');
        $fixture->setLocation('Value');
        $fixture->setContractType('Value');
        $fixture->setSalaryMin('Value');
        $fixture->setSalaryMax('Value');
        $fixture->setDepartment('Value');
        $fixture->setDatetime_immutable('Value');
        $fixture->setIsActive('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/job/offer/');
        self::assertSame(0, $this->jobOfferRepository->count([]));
    }
}
