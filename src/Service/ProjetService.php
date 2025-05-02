<?php

namespace App\Service;

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;

class ProjetService
{
    private const APPLICATION_NAME = 'Projet Manager';
    private const SERVICE_ACCOUNT_KEY_FILE = 'C:/Users/bnsih/Downloads/youcan-new-349411-7acddf9f90d2.json';
    private const CALENDAR_ID = 'bnsiheb666@gmail.com';

    private function getCalendarService(): Calendar
    {
        $client = new Client();
        $client->setApplicationName(self::APPLICATION_NAME);
        $client->setScopes([Calendar::CALENDAR]);
        $client->setAuthConfig(self::SERVICE_ACCOUNT_KEY_FILE);

        return new Calendar($client);
    }

    public function ajouterProjetToGoogleCalendar(string $nomProjet, string $descProjet, \DateTime $dateDebut, \DateTime $dateFin): string
    {
        $calendarService = $this->getCalendarService();

        $event = new Event([
            'summary' => $nomProjet,
            'description' => $descProjet,
            'start' => [
                'dateTime' => $dateDebut->format('c'),
                'timeZone' => 'UTC',
            ],
            'end' => [
                'dateTime' => $dateFin->format('c'),
                'timeZone' => 'UTC',
            ],
        ]);

        $createdEvent = $calendarService->events->insert(self::CALENDAR_ID, $event);

        return $createdEvent->getHtmlLink();
    }
}