<?php

namespace App\Services;

use App\Models\Entity;
use App\Models\FiscalDeadline;
use Eluceo\iCal\Domain\Entity\Calendar;
use Eluceo\iCal\Domain\Entity\Event;
use Eluceo\iCal\Domain\ValueObject\DateTime;
use Eluceo\iCal\Domain\ValueObject\TimeSpan;
use Eluceo\iCal\Presentation\Factory\CalendarFactory;

class ICalendarService
{
    public function generateFeed(Entity $entity)
    {
        $calendar = new Calendar();

        $deadlines = FiscalDeadline::where('entity_id', $entity->id)
            ->where('status', 'pending')
            ->get();

        foreach ($deadlines as $deadline) {
            $event = new Event();
            $event->setSummary($deadline->title);
            
            if ($deadline->description) {
                $event->setDescription($deadline->description);
            }

            $start = new DateTime(
                \DateTimeImmutable::createFromFormat('Y-m-d', $deadline->due_date->format('Y-m-d')),
                false
            );
            
            $event->setOccurrence(new TimeSpan($start, $start));

            $calendar->addEvent($event);
        }

        $componentFactory = new CalendarFactory();
        return $componentFactory->createCalendar($calendar);
    }
}
