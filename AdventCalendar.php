<?php

/**
 * Class AdventCalendar
 */
class AdventCalendar
{
    /**
     * @var string
     */
    private $calendarWidth;
    /**
     * @var string
     */
    private $calendarHeight;
    /**
     * @var array
     */
    private $entries;

    /**
     * Sets the config variables.
     *
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->calendarWidth = $config['calendarWidth'] ?: '500px';
        $this->calendarHeight = $config['calendarHeight'] ?: '840px';
    }

    /**
     * Adds a new entry to the calendar.
     *
     * @param array $entry
     */
    public function setEntry($entry)
    {
        if ($this->validateEntry($entry)) {
            $this->entries[] = $entry;
        }
    }

    /**
     * Checks if an entry can be added to the calendar.
     *
     * @param array $entry
     * @return bool
     */
    public function validateEntry($entry)
    {
        $arrayKeys = array(
            'unlockDate',
            'positionTop',
            'positionLeft',
            'url'
        );

        foreach ($arrayKeys as $arrayKey) {
            if (!array_key_exists($arrayKey, $entry)) {
                return false;
            }
        }

        if (!array_key_exists('doorImageLeft', $entry) && !array_key_exists('doorImageRight', $entry)) {
            return false;
        }

        return true;
    }

    /**
     * Loads a calendar from a json string.
     *
     * @param string $json
     */
    public function loadFromJson($json)
    {
        $data = json_decode($json, true);
        $this->setConfig($data['config']);
        /** @var array $entry */
        foreach ($data['entries'] as $entry) {
            $this->setEntry($entry);
        }
    }

    /**
     * Renders the HTML for the calendar.
     *
     * @param boolean $printOutput if set to TRUE, print output
     * @return string
     */
    public function render($printOutput = false)
    {
        $now = time();
        $output = '';

        foreach ($this->entries as $entry) {
            if ($entry['doorImageLeft'] && $entry['doorImageRight']) {
                $totalWidth = $entry['doorWidth'] * 2;
            } else {
                $totalWidth = $entry['doorWidth'];
            }
            if ($now >= strtotime($entry['unlockDate'])) {
                $output .= '<a href="' . $entry['url'] .
                    '" class="advent-calendar-entry" style="width:' . $totalWidth .
                    'px;top:' . $entry['positionTop'] . 'px;left:' . $entry['positionLeft'] . 'px;">';
                if ($entry['backgroundImage']) {
                    $output .= '<img class="advent-calendar-background" src="' .
                        $entry['backgroundImage'] . '" alt="" />';
                }
                if ($entry['doorImageLeft']) {
                    $output .= '<div class="advent-calendar-door-left-wrapper">' .
                        '<img class="advent-calendar-door" src="' . $entry['doorImageLeft'] . '" alt="" />' .
                        '</div>';
                }
                if ($entry['doorImageRight']) {
                    $output .= '<div class="advent-calendar-door-right-wrapper">' .
                        '<img class="advent-calendar-door" src="' . $entry['doorImageRight'] . '" alt="" /></div>';
                }
                $output .= '</a>';
            } else {
                $output .= '<div class="advent-calendar-entry" ' .
                    'style="width:' . $totalWidth . 'px;' .
                    'top:' . $entry['positionTop'] . 'px;' .
                    'left:' . $entry['positionLeft'] . 'px;">';
                if ($entry['doorImageLeft']) {
                    $output .= '<div class="advent-calendar-door-left-wrapper">' .
                        '<img class="advent-calendar-door" src="' . $entry['doorImageLeft'] . '" alt="" /></div>';
                }
                if ($entry['doorImageRight']) {
                    $output .= '<div class="advent-calendar-door-right-wrapper">' .
                        '<img class="advent-calendar-door" src="' . $entry['doorImageRight'] . '" alt="" /></div>';
                }
                $output .= '</div>';
            }
        }

        // wrap elements
        $output .= '<div class="advent-calendar" ' .
            'style="width:' . $this->calendarWidth . 'px;height:' . $this->calendarHeight . 'px;">' .
            $output .
            '</div>';

        if ($printOutput) {
            echo $output;
        }

        return $output;
    }
}
