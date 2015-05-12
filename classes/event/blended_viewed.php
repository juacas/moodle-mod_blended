<?php
// This file is part of Intuitel
//

/**
 * The mod_blended challenge submit event.
 *
 * @package    mod_blended
 * @copyright  2015 Juan Pablo de Castro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_blended\event;
require_once 'base.php';

defined('MOODLE_INTERNAL') || die();

/**
 * The user enters the Blended Control panel
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - string info
 *      - string cmid
 * }
 *
 * @package    mod_blended
 * @since      Moodle 2.7
 * @copyright  2015 Juan Pablo de Castro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class blended_viewed extends base {
    /**
     * 
     * @param int $courseid
     * @param int $userid
     * @param string $cmId
     * @return unknown
     */
    public static function create_from_parts($courseid,$userid, $cmId,$url,$info) {
        $data = array(
            'relateduserid' => $userid,
            'context' => \context_course::instance($courseid),
            'userid' => $userid,
            'objectid' =>$cmId,
            'courseid' => $courseid,
            'other' => array(
                'info' => $info,
                'cmid' => $cmId,
            ),
        );
        /** @var blended_viewed $event */
        $event = self::create($data);
        $event->set_legacy_logdata('view', $info, $url);  
        return $event;
    }

    /**
     * Init method.
     */
    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'blended';
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return "Blended control center viewed.";
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' entered Blended control panel '$this->objectid' " .
            "in the course '$this->courseid'. ".$this->data['other']['info'];
    }

 
    
    /**
     * Custom validation.
     *
     * @throws \coding_exception
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->relateduserid)) {
            throw new \coding_exception('The \'relateduserid\' must be set.');
        }
        if (!isset($this->other['info'])) {
            throw new \coding_exception('The \'info\' value must be set in other.');
        }
    }
}
