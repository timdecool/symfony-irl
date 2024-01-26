<?php

namespace App\EventSubscriber;

use App\Repository\AvailabilityRepository;
use CalendarBundle\CalendarEvents;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\CalendarEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CalendarSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private AvailabilityRepository $availabilityRepository,
        private UrlGeneratorInterface $router
    )
    {}

    public static function getSubscribedEvents()
    {
        return [
            CalendarEvents::SET_DATA => 'onCalendarSetData',
        ];
    }

    public function onCalendarSetData(CalendarEvent $calendar)
    {
        $start = $calendar->getStart();
        $end = $calendar->getEnd();
        $filters = $calendar->getFilters();

        // Modify the query to fit to your entity and needs
        // Change booking.beginAt by your start date property
        $availabilities = $this->availabilityRepository
            ->createQueryBuilder('availability')
            ->where('availability.beginAt BETWEEN :start and :end OR availability.endAt BETWEEN :start and :end')
            ->setParameter('start', $start->format('Y-m-d H:i:s'))
            ->setParameter('end', $end->format('Y-m-d H:i:s'))
            ->getQuery()
            ->getResult()
        ;

        foreach ($availabilities as $availability) {
            // this create the events with your data (here booking data) to fill calendar
            $availabilityEvent = new Event(
                'Disponibilité personnelle',
                $availability->getBeginAt(),
                $availability->getEndAt() // If the end date is null or not defined, a all day event is created.
            );

            /*
             * Add custom options to events
             *
             * For more information see: https://fullcalendar.io/docs/event-object
             * and: https://github.com/fullcalendar/fullcalendar/blob/master/src/core/options.ts
             */

            $availabilityEvent->setOptions([
                'backgroundColor' => 'red',
                'borderColor' => 'red',
            ]);
            $availabilityEvent->addOption(
                'url',
                $this->router->generate('app_availability_show', [
                    'id' => $availability->getId(),
                ])
            );

            // finally, add the event to the CalendarEvent to fill the calendar
            $calendar->addEvent($availabilityEvent);
        }
    }
}