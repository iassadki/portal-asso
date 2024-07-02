<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Repository\EvenementRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\CalendarEvent;
use CalendarBundle\CalendarEvents;


class CalendarSubscriber implements EventSubscriberInterface
{

    public function __construct(  private readonly EvenementRepository $bookingRepository,
    private readonly UrlGeneratorInterface $router)
    {
        // ...
    }

    public static function getSubscribedEvents()
    {
        return [
            CalendarEvents::SET_DATA => 'onCalendarSetData',        
        ];
    }

    public function onCalendarSetData(CalendarEvent $calendarEvent)
    {
        $startDate = $calendarEvent->getStart();
        $endDate = $calendarEvent->getEnd();
        $filters = $calendarEvent->getFilters();

        // You may want to make a custom query from your database to fill the calendar
        $bookings = $this->bookingRepository
            ->createQueryBuilder('evenement')
            ->where('evenement.dateDebut < :end')
            ->andWhere('evenement.dateFin > :start')
            ->setParameter('start', $startDate->format('Y-m-d H:i:s'))
            ->setParameter('end', $endDate->format('Y-m-d H:i:s'))
            ->getQuery()
            ->getResult();

        foreach($bookings as $booking) {
            // create an event with a start/end time, or an all day event
            $event = new Event(
                $booking->getNom(),
                $booking->getDateDebut(),
                $booking->getDateFin() // If the end date is null or not defined, a all day event is created.
            );

            //optional calendar event settings
            $event->setOptions([
                'backgroundColor' => 'red',
                'borderColor' => 'red',
            ]);
          

            // finally, add the event to the CalendarEvent to fill the calendar
            $calendarEvent->addEvent($event);
        }
    }
    

    public function onCalendarVisit($event)
    {
        // ...
    }
}
 