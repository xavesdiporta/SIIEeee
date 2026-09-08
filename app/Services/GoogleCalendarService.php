<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Google\Service\Calendar as GoogleCalendarApi;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class GoogleCalendarService
{
    protected GoogleClient $client;
    protected GoogleCalendarApi $service;
    protected string $calendarId;

    public function __construct()
    {
        $this->calendarId = config('services.google_calendar.calendar_id');

        $this->client = new GoogleClient();
        $this->client->setDeveloperKey(config('services.google_calendar.api_key'));
        $this->client->setApplicationName(config('app.name', 'Agrupamento'));

        $this->service = new GoogleCalendarApi($this->client);
    }

    /**
     * Devolve os próximos eventos do calendário do Clã, já formatados para a view.
     * Fica em cache 20 minutos para não gastar quota da API a cada visita ao dashboard.
     *
     * @return array<int, array{title:string, location:?string, start:Carbon, end:Carbon, all_day:bool, link:?string}>
     */
    public function getUpcomingEvents(int $maxResults = 5): array
    {
        return Cache::remember('cla.calendar.events', now()->addMinutes(20), function () use ($maxResults) {
            try {
                $response = $this->service->events->listEvents($this->calendarId, [
                    'timeMin'      => now()->toRfc3339String(),
                    'maxResults'   => $maxResults,
                    'singleEvents' => true,
                    'orderBy'      => 'startTime',
                ]);
            } catch (\Exception $e) {
                // Não deixar o dashboard rebentar se o Google estiver em baixo ou a key for inválida.
                report($e);

                return [];
            }

            $events = [];

            foreach ($response->getItems() as $event) {
                $isAllDay = is_null($event->getStart()->getDateTime());
                $start = $isAllDay ? $event->getStart()->getDate() : $event->getStart()->getDateTime();
                $end   = $isAllDay ? $event->getEnd()->getDate()   : $event->getEnd()->getDateTime();

                $events[] = [
                    'title'    => $event->getSummary() ?: 'Sem título',
                    'location' => $event->getLocation(),
                    'start'    => Carbon::parse($start),
                    'end'      => Carbon::parse($end),
                    'all_day'  => $isAllDay,
                    'link'     => $event->getHtmlLink(),
                ];
            }

            return $events;
        });
    }

    /**
     * Link direto para o calendário público, usado no botão "Ver calendário completo".
     */
    public function getPublicCalendarUrl(): string
    {
        return 'https://calendar.google.com/calendar/embed?src=' . urlencode($this->calendarId);
    }

    /**
     * Limpa a cache de eventos — útil chamar depois de criares/editares um evento manualmente,
     * ou a partir de um Job agendado que sincroniza periodicamente.
     */
    public function forgetCache(): void
    {
        Cache::forget('cla.calendar.events');
    }
}
