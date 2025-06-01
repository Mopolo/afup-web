<?php

declare(strict_types=1);

namespace AppBundle\Calendar;

use Sabre\VObject\Component\VCalendar;

class TechnoWatchCalendarGenerator
{
    public function __construct(
        private readonly string $name,
        private readonly \DateTime $currentDate,
    ) {}

    public function generate(string $googleSpreadsheetCsvUrl, bool $displayPrefix, ?string $filter = null): string
    {
        $eventLabelPrefix = $displayPrefix ? $this->name . " : " : "";

        $vcalendar = new VCalendar();
        $vcalendar->add('X-WR-CALNAME', $this->name);
        foreach ($this->prepareEvents($googleSpreadsheetCsvUrl, $filter) as $event) {
            $vcalendar->add('VEVENT', [
                'SUMMARY' => $eventLabelPrefix . $event->firstChair . " / " . $event->secondChair,
                'DTSTART;VALUE=DATE' => $event->date->format('Ymd'),
                'DESCRIPTION' => '',
                'TRANSP' => 'TRANSPARENT',
            ]);
        }

        return $vcalendar->serialize();
    }

    /**
     * @return array<TechnoWatchEvent>
     */
    private function prepareEvents(string $googleSpreadsheetCsvUrl, ?string $filter = null): array
    {
        $url = $googleSpreadsheetCsvUrl;

        $fp = fopen($url, 'rb');
        if (!$fp) {
            throw new \RuntimeException("Error opening spreadsheet");
        }

        $events = [];

        while (false !== ($row = fgetcsv($fp))) {
            $rawDate = trim((string) $row[0]);

            if ($rawDate === '') {
                continue;
            }

            $date = \DateTimeImmutable::createFromFormat('d/m/Y', $rawDate);

            if (false === $date || $date < $this->currentDate) {
                continue;
            }

            $firstChair = trim((string) $row[4]);
            $secondChair = trim((string) $row[5]);

            if (strlen((string) $filter) > 0 && ($firstChair !== $filter && $secondChair !== $filter)) {
                continue;
            }

            if ($firstChair === '' || $secondChair === '') {
                continue;
            }

            $events[] = new TechnoWatchEvent($date, $row[4], $row[5]);
        }

        return $events;
    }
}
