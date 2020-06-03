<?php
// This file is part of the Zoom plugin for Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * List all zoom recordings.
 *
 * @package    mod_zoom
 * @copyright  2015 UC Regents
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');

$cmid = required_param('cmid', PARAM_INT);

list ($course, $cm) = get_course_and_cm_from_cmid($cmid, 'zoom');

require_login($course, true, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/zoom:view', $context);

$zoom  = $DB->get_record('zoom', array('id' => $cm->instance), '*', MUST_EXIST);

$PAGE->set_context($context);
$PAGE->set_url('/mod/zoom/recordings.php', array('cmid' => $cm->id));
$PAGE->set_title(format_string($zoom->name));
$PAGE->set_heading(format_string($course->fullname));

$sql = "SELECT * FROM {zoom_meeting_recordings} rec " .
        "JOIN {zoom_meeting_details} d ON d.uuid = rec.meetinguuid " .
        "WHERE d.zoomid = :cmid ";
$recordings = $DB->get_records_sql($sql, array('cmid' => $cm->id));

$table = new html_table();
$table->head = ['TOPIC', 'START', 'END', 'RECORDING_TYPE', 'PLAY_URL', 'DOWNLOAD_URL'];
$table->attributes['class'] = 'generaltable mod_view';
foreach ($recordings as $recording) {
    $row = [
            $recording->topic,
            format_time($recording->recording_start),
            format_time($recording->recording_end),
            $recording->recording_type,
            html_writer::link($recording->play_url, "PLAY"),
            html_writer::link($recording->download_url, "DOWNLOAD")
    ];
    $table->data[] = $row;
}

echo $OUTPUT->header();
echo $OUTPUT->heading("RECORDINGS FOR " . htmlentities("$zoom->name"));
echo html_writer::table($table);
echo $OUTPUT->footer();