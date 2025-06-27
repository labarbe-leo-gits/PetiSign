<?php
class CalendarEvent {
    private $title;
    private $description;
    private $startDateTime;
    private $endDateTime;
    private $location;
    private $isAllDay;
    
    public function __construct($title, $description, $startDateTime, $endDateTime, $location = '', $isAllDay = false) {
        $this->title = $title;
        $this->description = $description;
        $this->startDateTime = $startDateTime;
        $this->endDateTime = $endDateTime;
        $this->location = $location;
        
        if (!$isAllDay) {
            $startTime = date('H:i:s', strtotime($this->startDateTime));
            $endTime = date('H:i:s', strtotime($this->endDateTime));
            $this->isAllDay = ($startTime === '00:00:00' && $endTime === '00:00:00');
        } else {
            $this->isAllDay = $isAllDay;
        }
    }

    public function generateICS() {
        $dtstamp = gmdate('Ymd\THis\Z');
        $uid = uniqid() . '@' . $_SERVER['HTTP_HOST'];
        
        if ($this->isAllDay) {
            $dtstart = date('Ymd', strtotime($this->startDateTime));
            $dtend = date('Ymd', strtotime($this->endDateTime . ' +1 day'));
        } else {
            $dtstart = gmdate('Ymd\THis\Z', strtotime($this->startDateTime));
            $dtend = gmdate('Ymd\THis\Z', strtotime($this->endDateTime));
        }
        
        $ics = "BEGIN:VCALENDAR\r\n";
        $ics .= "VERSION:2.0\r\n";
        $ics .= "PRODID:-//Calendar Event//EN\r\n";
        $ics .= "CALSCALE:GREGORIAN\r\n";
        $ics .= "METHOD:PUBLISH\r\n";
        $ics .= "BEGIN:VEVENT\r\n";
        $ics .= "UID:$uid\r\n";
        $ics .= "DTSTAMP:$dtstamp\r\n";
        
        if ($this->isAllDay) {
            $ics .= "DTSTART;VALUE=DATE:$dtstart\r\n";
            $ics .= "DTEND;VALUE=DATE:$dtend\r\n";
        } else {
            $ics .= "DTSTART:$dtstart\r\n";
            $ics .= "DTEND:$dtend\r\n";
        }
        
        $ics .= "SUMMARY:" . $this->escapeString($this->title) . "\r\n";
        $ics .= "DESCRIPTION:" . $this->escapeString($this->description) . "\r\n";
        
        if (!empty($this->location)) {
            $ics .= "LOCATION:" . $this->escapeString($this->location) . "\r\n";
        }
        
        $ics .= "STATUS:CONFIRMED\r\n";
        $ics .= "SEQUENCE:0\r\n";
        $ics .= "END:VEVENT\r\n";
        $ics .= "END:VCALENDAR\r\n";
        
        return $ics;
    }
    
    public function getGoogleCalendarUrl() {
        if ($this->isAllDay) {
            $startDate = date('Ymd', strtotime($this->startDateTime));
            $endDate = date('Ymd', strtotime($this->endDateTime . ' +1 day'));
        } else {
            $startDate = gmdate('Ymd\THis\Z', strtotime($this->startDateTime));
            $endDate = gmdate('Ymd\THis\Z', strtotime($this->endDateTime));
        }
        
        return "https://calendar.google.com/calendar/render?action=TEMPLATE" .
               "&text=" . urlencode($this->title) .
               "&dates=" . $startDate . "/" . $endDate .
               "&details=" . urlencode($this->description) .
               "&location=" . urlencode($this->location);
    }
    
    public function getOutlookUrl() {
        if ($this->isAllDay) {
            $startDate = date('Y-m-d', strtotime($this->startDateTime));
            $endDate = date('Y-m-d', strtotime($this->endDateTime . ' +1 day'));
            $allDay = '&allday=true';
        } else {
            $startDate = gmdate('c', strtotime($this->startDateTime));
            $endDate = gmdate('c', strtotime($this->endDateTime));
            $allDay = '';
        }
        
        return "https://outlook.live.com/calendar/0/deeplink/compose" .
               "?subject=" . urlencode($this->title) .
               "&startdt=" . $startDate .
               "&enddt=" . $endDate .
               "&body=" . urlencode($this->description) .
               "&location=" . urlencode($this->location) .
               $allDay;
    }
    
    public function getYahooUrl() {
        if ($this->isAllDay) {
            $startDate = date('Ymd', strtotime($this->startDateTime));
            $duration = '2400';
        } else {
            $startDate = gmdate('Ymd\THis', strtotime($this->startDateTime));
            $duration = (strtotime($this->endDateTime) - strtotime($this->startDateTime)) / 3600;
            $duration = sprintf('%02d%02d', floor($duration), ($duration - floor($duration)) * 60);
        }
        
        return "https://calendar.yahoo.com/?v=60&view=d&type=20" .
               "&title=" . urlencode($this->title) .
               "&st=" . $startDate .
               "&dur=" . $duration .
               "&desc=" . urlencode($this->description) .
               "&in_loc=" . urlencode($this->location);
    }
    
    private function escapeString($string) {
        $string = str_replace(["\r\n", "\n", "\r"], "\\n", $string);
        $string = str_replace(["\\", ",", ";"], ["\\\\", "\\,", "\\;"], $string);
        return $string;
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'download') {
    $event = new CalendarEvent(
        $_GET['title'] ?? 'Test Event',
        $_GET['description'] ?? 'Test Description',
        $_GET['start'] ?? date('Y-m-d H:i:s'),
        $_GET['end'] ?? date('Y-m-d H:i:s', strtotime('+1 hour')),
        $_GET['location'] ?? 'Test Location'
    );
    
    $icsContent = $event->generateICS();
    
    if (isset($_GET['debug'])) {
        echo "<pre>";
        echo "Content Length: " . strlen($icsContent) . "\n";
        echo "Content Preview:\n";
        echo htmlspecialchars($icsContent);
        echo "</pre>";
        exit;
    }
    
    $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $_GET['title'] ?? 'evenement') . '.ics';
    
    header('Content-Type: text/calendar; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($icsContent));
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
    
    echo $icsContent;
    exit;
}
?>