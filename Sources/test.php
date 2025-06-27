<?php

class CalendarEvent {
    private $title;
    private $description;
    private $startDateTime;
    private $endDateTime;
    private $location;
    
    public function __construct($title, $description, $startDateTime, $endDateTime, $location = '') {
        $this->title = $title;
        $this->description = $description;
        $this->startDateTime = $startDateTime;
        $this->endDateTime = $endDateTime;
        $this->location = $location;
    }
    
    public function generateICS() {
        $dtstart = gmdate('Ymd\THis\Z', strtotime($this->startDateTime));
        $dtend = gmdate('Ymd\THis\Z', strtotime($this->endDateTime));
        $dtstamp = gmdate('Ymd\THis\Z');
        $uid = uniqid() . '@' . $_SERVER['HTTP_HOST'];
        
        $ics = "BEGIN:VCALENDAR\r\n";
        $ics .= "VERSION:2.0\r\n";
        $ics .= "PRODID:-//Calendar Event//EN\r\n";
        $ics .= "CALSCALE:GREGORIAN\r\n";
        $ics .= "METHOD:PUBLISH\r\n";
        $ics .= "BEGIN:VEVENT\r\n";
        $ics .= "UID:$uid\r\n";
        $ics .= "DTSTAMP:$dtstamp\r\n";
        $ics .= "DTSTART:$dtstart\r\n";
        $ics .= "DTEND:$dtend\r\n";
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
        $startDate = gmdate('Ymd\THis\Z', strtotime($this->startDateTime));
        $endDate = gmdate('Ymd\THis\Z', strtotime($this->endDateTime));
        
        return "https://calendar.google.com/calendar/render?action=TEMPLATE" .
               "&text=" . urlencode($this->title) .
               "&dates=" . $startDate . "/" . $endDate .
               "&details=" . urlencode($this->description) .
               "&location=" . urlencode($this->location);
    }
    
    public function getOutlookUrl() {
        $startDate = gmdate('c', strtotime($this->startDateTime));
        $endDate = gmdate('c', strtotime($this->endDateTime));
        
        return "https://outlook.live.com/calendar/0/deeplink/compose" .
               "?subject=" . urlencode($this->title) .
               "&startdt=" . $startDate .
               "&enddt=" . $endDate .
               "&body=" . urlencode($this->description) .
               "&location=" . urlencode($this->location);
    }
    
    public function getYahooUrl() {
        $startDate = gmdate('Ymd\THis', strtotime($this->startDateTime));
        $duration = (strtotime($this->endDateTime) - strtotime($this->startDateTime)) / 3600; // hours
        $durationFormatted = sprintf('%02d%02d', floor($duration), ($duration - floor($duration)) * 60);
        
        return "https://calendar.yahoo.com/?v=60&view=d&type=20" .
               "&title=" . urlencode($this->title) .
               "&st=" . $startDate .
               "&dur=" . $durationFormatted .
               "&desc=" . urlencode($this->description) .
               "&in_loc=" . urlencode($this->location);
    }
    
    private function escapeString($string) {
        return addcslashes($string, "\r\n,;\\");
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'download') {
    $event = new CalendarEvent(
        $_GET['title'] ?? 'Event',
        $_GET['description'] ?? '',
        $_GET['start'] ?? date('Y-m-d H:i:s'),
        $_GET['end'] ?? date('Y-m-d H:i:s', strtotime('+1 hour')),
        $_GET['location'] ?? ''
    );
    
    $icsContent = $event->generateICS();
    $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $_GET['title'] ?? 'event') . '.ics';
    
    header('Content-Type: text/calendar; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($icsContent));
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
    
    echo $icsContent;
    exit;
}

// Sample event data (you can modify this or get from database/form)
$eventTitle = 'Team Meeting';
$eventDescription = 'Weekly team sync meeting to discuss project progress and upcoming deadlines';
$eventStart = '2024-12-15 14:00:00';
$eventEnd = '2024-12-15 15:30:00';
$eventLocation = 'Conference Room A, 5th Floor';

$event = new CalendarEvent($eventTitle, $eventDescription, $eventStart, $eventEnd, $eventLocation);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add to Calendar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .event-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .event-title {
            color: #333;
            font-size: 28px;
            margin-bottom: 15px;
            border-bottom: 2px solid #4285f4;
            padding-bottom: 10px;
        }
        
        .event-details {
            margin-bottom: 25px;
        }
        
        .event-detail {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        
        .detail-label {
            font-weight: bold;
            color: #555;
            width: 100px;
            flex-shrink: 0;
        }
        
        .detail-value {
            color: #333;
        }
        
        .calendar-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 30px;
        }
        
        .calendar-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 8px;
            color: white;
            font-weight: bold;
            font-size: 14px;
            transition: all 0.3s ease;
            text-align: center;
            min-height: 50px;
            box-sizing: border-box;
        }
        
        .calendar-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        
        .ics-btn { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .google-btn { 
            background: linear-gradient(135deg, #4285f4 0%, #34a853 100%);
        }
        .outlook-btn { 
            background: linear-gradient(135deg, #0078d4 0%, #106ebe 100%);
        }
        .yahoo-btn { 
            background: linear-gradient(135deg, #7b68ee 0%, #9966cc 100%);
        }
        
        .instructions {
            background: #e8f4fd;
            border-left: 4px solid #4285f4;
            padding: 15px;
            margin-top: 20px;
            border-radius: 4px;
        }
        
        .instructions h3 {
            margin-top: 0;
            color: #1565c0;
        }
        
        @media (max-width: 600px) {
            .calendar-buttons {
                grid-template-columns: 1fr;
            }
            
            .event-detail {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .detail-label {
                width: auto;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="event-card">
        <h1 class="event-title"><?php echo htmlspecialchars($eventTitle); ?></h1>
        
        <div class="event-details">
            <div class="event-detail">
                <span class="detail-label">📅 Date:</span>
                <span class="detail-value"><?php echo date('F j, Y', strtotime($eventStart)); ?></span>
            </div>
            <div class="event-detail">
                <span class="detail-label">🕐 Time:</span>
                <span class="detail-value">
                    <?php echo date('g:i A', strtotime($eventStart)); ?> - 
                    <?php echo date('g:i A', strtotime($eventEnd)); ?>
                </span>
            </div>
            <?php if (!empty($eventLocation)): ?>
            <div class="event-detail">
                <span class="detail-label">📍 Location:</span>
                <span class="detail-value"><?php echo htmlspecialchars($eventLocation); ?></span>
            </div>
            <?php endif; ?>
            <div class="event-detail">
                <span class="detail-label">📝 Details:</span>
                <span class="detail-value"><?php echo htmlspecialchars($eventDescription); ?></span>
            </div>
        </div>
        
        <div class="calendar-buttons">
            <a href="?action=download&title=<?php echo urlencode($eventTitle); ?>&description=<?php echo urlencode($eventDescription); ?>&start=<?php echo urlencode($eventStart); ?>&end=<?php echo urlencode($eventEnd); ?>&location=<?php echo urlencode($eventLocation); ?>" 
               class="calendar-btn ics-btn">
                📥 Download Calendar File
            </a>
            
            <a href="<?php echo $event->getGoogleCalendarUrl(); ?>" 
               target="_blank" 
               class="calendar-btn google-btn">
                📅 Google Calendar
            </a>
            
            <a href="<?php echo $event->getOutlookUrl(); ?>" 
               target="_blank" 
               class="calendar-btn outlook-btn">
                📧 Outlook Calendar
            </a>
            
            <a href="<?php echo $event->getYahooUrl(); ?>" 
               target="_blank" 
               class="calendar-btn yahoo-btn">
                💜 Yahoo Calendar
            </a>
        </div>
        
        <div class="instructions">
            <h3>How to add this event:</h3>
            <ul>
                <li><strong>Download Calendar File:</strong> Click to download a .ics file that works with most calendar apps</li>
                <li><strong>Web Calendars:</strong> Click any of the other buttons to open directly in your preferred online calendar</li>
                <li><strong>Mobile:</strong> The calendar file will open in your default calendar app on mobile devices</li>
            </ul>
        </div>
    </div>
    
    <script>
        // Optional: Add some interactivity
        document.addEventListener('DOMContentLoaded', function() {
            // Add click tracking or analytics here if needed
            const buttons = document.querySelectorAll('.calendar-btn');
            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    // You can add tracking or confirmation here
                    console.log('Calendar button clicked:', this.textContent.trim());
                });
            });
        });
    </script>
</body>
</html>