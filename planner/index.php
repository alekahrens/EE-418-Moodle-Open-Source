<?php
// This file is part of Moodle - http://moodle.org/
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
 * This page is provided for compatability and redirects the user to the default grade report
 *
 * @package   core
 * @copyright 2005 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once '../config.php';
require_once($CFG->dirroot . '/user/lib.php');

require_login(null, false);

$id             = $USER->id;
$courseid       = optional_param('course', SITEID, PARAM_INT); // course id (defaults to Site).
$showallcourses = optional_param('showallcourses', 0, PARAM_INT);

$PAGE->set_url('/user/view.php', array('id' => $id, 'course' => $courseid));
$PAGE->set_pagetype('course-view');

if (isguestuser()) {
    throw new require_login_exception('Guests are not allowed here.');
}

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourseid');
}

$context = context_course::instance($course->id);

$PAGE->set_context($context);
echo $OUTPUT->header();
echo $OUTPUT->heading('Assignments Overview');

$renderer = $PAGE->get_renderer('core');
$planner = new planner_results($id);
echo $renderer->render_planner($planner);

echo $OUTPUT->footer();










