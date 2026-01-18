<?php

class MBLShift
{
    /**
     * @var MBLMetaInterface
     */
    private $page;

    /**
     * @var int
     */
    private $startDate;
    private $metaKey;

    /**
     * MBLShift constructor.
     * @param MBLMetaInterface $page
     * @param $startDate integer timestamp
     * @param $metaKey
     */
    public function __construct(MBLMetaInterface $page, $startDate, $metaKey = 'homework_shift_value')
    {
        $this->page = $page;
        $this->startDate = $startDate;
        $this->metaKey = $metaKey;
    }


    public function getEndDate()
    {
        try {
            switch ($this->page->getMeta($this->metaKey . '_type')) {
                case 'interval':
                    $endDate = $this->_endDateByInterval();
                    break;

                case 'time':
                    $endDate = $this->_endDateByTime();
                    break;

                case 'weekday':
                    $endDate = $this->_endDateByWeekday();
                    break;

                case 'day':
                    $endDate = $this->_endDateByDay();
                    break;

                case 'date':
                    $endDate = $this->_endDateByDate();
                    break;

                default:
                    $endDate = new DateTime();
            }
        } catch (Exception $exception) {
            $endDate = new DateTime();
        }

        return $endDate;
    }


    /**
     * @return DateTime
     * @throws Exception
     */
    private function _endDateByInterval()
    {
        $interval = intval($this->page->getMeta($this->metaKey, 0) * 60 * 60);
        return new DateTime('@' . ($this->startDate + $interval));
    }

    /**
     * @return DateTime
     * @throws Exception
     */
    private function _endDateByTime()
    {
        $hours = intval($this->page->getMeta($this->metaKey . '_time_hours', 0));
        $minutes = intval($this->page->getMeta($this->metaKey . '_time_minutes', 0));

        $dtStart = new DateTime('@' . $this->startDate);

        $endDate = new DateTime('@' . $this->startDate);
        $endDate->setTime($hours, $minutes);

        if($dtStart > $endDate) {
            $endDate->modify('+1 day');
        }

        return $endDate;
    }


    /**
     * @return DateTime
     * @throws Exception
     */
    private function _endDateByWeekday()
    {
        $day = intval($this->page->getMeta($this->metaKey . '_weekday_day', 0));
        $hours = intval($this->page->getMeta($this->metaKey . '_weekday_hours', 0));
        $minutes = intval($this->page->getMeta($this->metaKey . '_weekday_minutes', 0));
        $weekDay = self::getWeekDayByNumber($day);

        //check current day
        $curDtStart = new DateTime('@' . $this->startDate);
        $curDtEnd = new DateTime('@' . $this->startDate);
        $curDtEnd->setTime($hours, $minutes);

        if($curDtEnd->format('l') == $weekDay && $curDtEnd >= $curDtStart) {
            return $curDtEnd;
        }

        $endDate = new DateTime('@' . $this->startDate);
        $endDate->modify('next ' . $weekDay);
        $endDate->setTime($hours, $minutes);

        return $endDate;
    }


    /**
     * @return DateTime
     * @throws Exception
     */
    private function _endDateByDay()
    {
        $day = intval($this->page->getMeta($this->metaKey . '_day_day', 0));
        $hours = intval($this->page->getMeta($this->metaKey . '_day_hours', 0));
        $minutes = intval($this->page->getMeta($this->metaKey . '_day_minutes', 0));

        $dtStart = new DateTime('@' . $this->startDate);

        $endDate = new DateTime('@' . $this->startDate);
        $endDate->modify('first day of this month');
        //clears internal day cursor
        $endDate->modify('+0 month');
        $backupDate = clone $endDate;

        $endDate->setDate($dtStart->format('Y'), $dtStart->format('n'), $day);
        $endDate->setTime($hours, $minutes);

        while ($endDate < $dtStart || $endDate->format('j') != $day) {
            $endDate = clone $backupDate;
            $endDate->modify('first day of this month');
            $endDate->modify('+1 month');
            $backupDate = clone $endDate;
            $endDate->setDate($endDate->format('Y'), $endDate->format('n'), $day);
            $endDate->setTime($hours, $minutes);
        }

        return $endDate;
    }

    /**
     * @return DateTime
     * @throws Exception
     */
    private function _endDateByDate()
    {
        $date = $this->page->getMeta($this->metaKey . '_date_date', date('d.m.Y'));
        $hours = intval($this->page->getMeta($this->metaKey . '_date_hours', 0));
        $minutes = intval($this->page->getMeta($this->metaKey . '_date_minutes', 0));

        $endDate = date_create_from_format('d.m.Y', $date);

        if($endDate) {
            $endDate->setTime($hours, $minutes);
        }

        return $endDate;
    }

    public static function getWeekDayByNumber($number)
    {
        return date('l', strtotime("Sunday +{$number} days"));
    }
}