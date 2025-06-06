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
 * Events tests.
 *
 * @package mod_data
 * @category test
 * @copyright 2014 Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_data\event;

use mod_data\local\importer\preset_existing_importer;
use mod_data\manager;
use mod_data\preset;

final class events_test extends \advanced_testcase {

    /**
     * Test set up.
     *
     * This is executed before running any test in this file.
     */
    public function setUp(): void {
        $this->resetAfterTest();
    }

    /**
     * Test the field created event.
     */
    public function test_field_created() {
        $this->setAdminUser();

        // Create a course we are going to add a data module to.
        $course = $this->getDataGenerator()->create_course();

        // The generator used to create a data module.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_data');

        // Create a data module.
        $data = $generator->create_instance(array('course' => $course->id));

        // Now we want to create a field.
        $field = data_get_field_new('text', $data);
        $fielddata = new \stdClass();
        $fielddata->name = 'Test';
        $fielddata->description = 'Test description';
        $field->define_field($fielddata);

        // Trigger and capture the event for creating a field.
        $sink = $this->redirectEvents();
        $field->insert_field();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_data\event\field_created', $event);
        $this->assertEquals(\context_module::instance($data->cmid), $event->get_context());
        $expected = array($course->id, 'data', 'fields add', 'field.php?d=' . $data->id . '&amp;mode=display&amp;fid=' .
            $field->field->id, $field->field->id, $data->cmid);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
        $url = new \moodle_url('/mod/data/field.php', array('d' => $data->id));
        $this->assertEquals($url, $event->get_url());
    }

    /**
     * Test the field updated event.
     */
    public function test_field_updated() {
        $this->setAdminUser();

        // Create a course we are going to add a data module to.
        $course = $this->getDataGenerator()->create_course();

        // The generator used to create a data module.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_data');

        // Create a data module.
        $data = $generator->create_instance(array('course' => $course->id));

        // Now we want to create a field.
        $field = data_get_field_new('text', $data);
        $fielddata = new \stdClass();
        $fielddata->name = 'Test';
        $fielddata->description = 'Test description';
        $field->define_field($fielddata);
        $field->insert_field();

        // Trigger and capture the event for updating the field.
        $sink = $this->redirectEvents();
        $field->update_field();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_data\event\field_updated', $event);
        $this->assertEquals(\context_module::instance($data->cmid), $event->get_context());
        $expected = array($course->id, 'data', 'fields update', 'field.php?d=' . $data->id . '&amp;mode=display&amp;fid=' .
            $field->field->id, $field->field->id, $data->cmid);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
        $url = new \moodle_url('/mod/data/field.php', array('d' => $data->id));
        $this->assertEquals($url, $event->get_url());
    }

    /**
     * Test the field deleted event.
     */
    public function test_field_deleted() {
        $this->setAdminUser();

        // Create a course we are going to add a data module to.
        $course = $this->getDataGenerator()->create_course();

        // The generator used to create a data module.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_data');

        // Create a data module.
        $data = $generator->create_instance(array('course' => $course->id));

        // Now we want to create a field.
        $field = data_get_field_new('text', $data);
        $fielddata = new \stdClass();
        $fielddata->name = 'Test';
        $fielddata->description = 'Test description';
        $field->define_field($fielddata);
        $field->insert_field();

        // Trigger and capture the event for deleting the field.
        $sink = $this->redirectEvents();
        $field->delete_field();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_data\event\field_deleted', $event);
        $this->assertEquals(\context_module::instance($data->cmid), $event->get_context());
        $expected = array($course->id, 'data', 'fields delete', 'field.php?d=' . $data->id, $field->field->name, $data->cmid);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
        $url = new \moodle_url('/mod/data/field.php', array('d' => $data->id));
        $this->assertEquals($url, $event->get_url());
    }

    /**
     * Test the record created event.
     */
    public function test_record_created() {
        // Create a course we are going to add a data module to.
        $course = $this->getDataGenerator()->create_course();

        // The generator used to create a data module.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_data');

        // Create a data module.
        $data = $generator->create_instance(array('course' => $course->id));

        // Trigger and capture the event for creating the record.
        $sink = $this->redirectEvents();
        $recordid = data_add_record($data);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_data\event\record_created', $event);
        $this->assertEquals(\context_module::instance($data->cmid), $event->get_context());
        $expected = array($course->id, 'data', 'add', 'view.php?d=' . $data->id . '&amp;rid=' . $recordid,
            $data->id, $data->cmid);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
        $url = new \moodle_url('/mod/data/view.php', array('d' => $data->id, 'rid' => $recordid));
        $this->assertEquals($url, $event->get_url());
    }

    /**
     * Test the record updated event.
     *
     * There is no external API for updating a record, so the unit test will simply create
     * and trigger the event and ensure the legacy log data is returned as expected.
     */
    public function test_record_updated() {
        // Create a course we are going to add a data module to.
        $course = $this->getDataGenerator()->create_course();

        // The generator used to create a data module.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_data');

        // Create a data module.
        $data = $generator->create_instance(array('course' => $course->id));

        // Trigger an event for updating this record.
        $event = \mod_data\event\record_updated::create(array(
            'objectid' => 1,
            'context' => \context_module::instance($data->cmid),
            'courseid' => $course->id,
            'other' => array(
                'dataid' => $data->id
            )
        ));

        // Trigger and capture the event for updating the data record.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_data\event\record_updated', $event);
        $this->assertEquals(\context_module::instance($data->cmid), $event->get_context());
        $expected = array($course->id, 'data', 'update', 'view.php?d=' . $data->id . '&amp;rid=1', $data->id, $data->cmid);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
        $url = new \moodle_url('/mod/data/view.php', array('d' => $data->id, 'rid' => $event->objectid));
        $this->assertEquals($url, $event->get_url());
    }

    /**
     * Test the record deleted event.
     */
    public function test_record_deleted() {
        global $DB;

        // Create a course we are going to add a data module to.
        $course = $this->getDataGenerator()->create_course();

        // The generator used to create a data module.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_data');

        // Create a data module.
        $data = $generator->create_instance(array('course' => $course->id));

        // Now we want to create a field.
        $field = data_get_field_new('text', $data);
        $fielddata = new \stdClass();
        $fielddata->name = 'Test';
        $fielddata->description = 'Test description';
        $field->define_field($fielddata);
        $field->insert_field();

        // Create data record.
        $datarecords = new \stdClass();
        $datarecords->userid = '2';
        $datarecords->dataid = $data->id;
        $datarecords->id = $DB->insert_record('data_records', $datarecords);

        // Create data content.
        $datacontent = new \stdClass();
        $datacontent->fieldid = $field->field->id;
        $datacontent->recordid = $datarecords->id;
        $datacontent->id = $DB->insert_record('data_content', $datacontent);

        // Trigger and capture the event for deleting the data record.
        $sink = $this->redirectEvents();
        data_delete_record($datarecords->id, $data, $course->id, $data->cmid);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_data\event\record_deleted', $event);
        $this->assertEquals(\context_module::instance($data->cmid), $event->get_context());
        $expected = array($course->id, 'data', 'record delete', 'view.php?id=' . $data->cmid, $data->id, $data->cmid);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
        $url = new \moodle_url('/mod/data/view.php', array('d' => $data->id));
        $this->assertEquals($url, $event->get_url());
    }

    /**
     * Test the template viewed event.
     *
     * There is no external API for viewing templates, so the unit test will simply create
     * and trigger the event and ensure the legacy log data is returned as expected.
     */
    public function test_template_viewed() {
        // Create a course we are going to add a data module to.
        $course = $this->getDataGenerator()->create_course();

        // The generator used to create a data module.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_data');

        // Create a data module.
        $data = $generator->create_instance(array('course' => $course->id));

        // Trigger an event for updating this record.
        $event = \mod_data\event\template_viewed::create(array(
            'context' => \context_module::instance($data->cmid),
            'courseid' => $course->id,
            'other' => array(
                'dataid' => $data->id
            )
        ));

        // Trigger and capture the event for updating the data record.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_data\event\template_viewed', $event);
        $this->assertEquals(\context_module::instance($data->cmid), $event->get_context());
        $expected = array($course->id, 'data', 'templates view', 'templates.php?id=' . $data->cmid . '&amp;d=' .
            $data->id, $data->id, $data->cmid);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
        $url = new \moodle_url('/mod/data/templates.php', array('d' => $data->id));
        $this->assertEquals($url, $event->get_url());
    }

    /**
     * Test the template updated event.
     *
     * There is no external API for updating a template, so the unit test will simply create
     * and trigger the event and ensure the legacy log data is returned as expected.
     */
    public function test_template_updated() {
        // Create a course we are going to add a data module to.
        $course = $this->getDataGenerator()->create_course();

        // The generator used to create a data module.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_data');

        // Create a data module.
        $data = $generator->create_instance(array('course' => $course->id));

        // Trigger an event for updating this record.
        $event = \mod_data\event\template_updated::create(array(
            'context' => \context_module::instance($data->cmid),
            'courseid' => $course->id,
            'other' => array(
                'dataid' => $data->id,
            )
        ));

        // Trigger and capture the event for updating the data record.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_data\event\template_updated', $event);
        $this->assertEquals(\context_module::instance($data->cmid), $event->get_context());
        $expected = array($course->id, 'data', 'templates saved', 'templates.php?id=' . $data->cmid . '&amp;d=' .
            $data->id, $data->id, $data->cmid);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
        $url = new \moodle_url('/mod/data/templates.php', array('d' => $data->id));
        $this->assertEquals($url, $event->get_url());
    }

    /**
     * Data provider for build providers for test_needs_mapping and test_set_affected_fields.
     *
     * @return array[]
     */
    public static function preset_importer_provider(): array {
        // Image gallery preset is: ['title' => 'text', 'description' => 'textarea', 'image' => 'picture'];

        $titlefield = new \stdClass();
        $titlefield->name = 'title';
        $titlefield->type = 'text';

        $descfield = new \stdClass();
        $descfield->name = 'description';
        $descfield->type = 'textarea';

        $imagefield = new \stdClass();
        $imagefield->name = 'image';
        $imagefield->type = 'picture';

        $difffield = new \stdClass();
        $difffield->name = 'title';
        $difffield->type = 'textarea';

        $newfield = new \stdClass();
        $newfield->name = 'number';
        $newfield->type = 'number';

        return [
            'Empty database / Importer with fields' => [
                'currentfields' => [],
                'newfields' => [$titlefield, $descfield, $imagefield],
                'expected' => ['field_created' => 3],
            ],
            'Database with fields / Empty importer' => [
                'currentfields' => [$titlefield, $descfield, $imagefield],
                'newfields' => [],
                'expected' => ['field_deleted' => 3],
            ],
            'Fields to create' => [
                'currentfields' => [$titlefield, $descfield],
                'newfields' => [$titlefield, $descfield, $imagefield],
                'expected' => ['field_updated' => 2, 'field_created' => 1],
            ],
            'Fields to remove' => [
                'currentfields' => [$titlefield, $descfield, $imagefield, $difffield],
                'newfields' => [$titlefield, $descfield, $imagefield],
                'expected' => ['field_updated' => 2, 'field_deleted' => 1],
            ],
            'Fields to update' => [
                'currentfields' => [$difffield, $descfield, $imagefield],
                'newfields' => [$titlefield, $descfield, $imagefield],
                'expected' => ['field_updated' => 1, 'field_created' => 1, 'field_deleted' => 1],
            ],
            'Fields to create, remove and update' => [
                'currentfields' => [$titlefield, $descfield, $imagefield, $difffield],
                'newfields' => [$titlefield, $descfield, $newfield],
                'expected' => ['field_updated' => 2, 'field_created' => 1, 'field_deleted' => 2],
            ],
        ];
    }
    /**
     * Test for needs_mapping method.
     *
     * @dataProvider preset_importer_provider
     *
     * @param array $currentfields Fields of the current activity.
     * @param array $newfields Fields to be imported.
     * @param array $expected Expected events.
     */
    public function test_importing_events(
        array $currentfields,
        array $newfields,
        array $expected
    ) {

        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();
        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_data');

        // Create a course and a database activity.
        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module(manager::MODULE, ['course' => $course]);
        // Add current fields to the activity.
        foreach ($currentfields as $field) {
            $plugingenerator->create_field($field, $activity);
        }
        $manager = manager::create_from_instance($activity);

        $presetactivity = $this->getDataGenerator()->create_module(manager::MODULE, ['course' => $course]);
        // Add current fields to the activity.
        foreach ($newfields as $field) {
            $plugingenerator->create_field($field, $presetactivity);
        }

        $record = (object) [
            'name' => 'Testing preset name',
            'description' => 'Testing preset description',
        ];
        $saved = $plugingenerator->create_preset($presetactivity, $record);
        $savedimporter = new preset_existing_importer($manager, $USER->id . '/Testing preset name');

        // Trigger and capture the event for deleting the field.
        $sink = $this->redirectEvents();
        $savedimporter->import(false);
        $events = $sink->get_events();

        foreach ($expected as $triggeredevent => $count) {
            for ($i = 0; $i < $count; $i++) {
                $event = array_shift($events);

                // Check that the event data is valid.
                $this->assertInstanceOf('\mod_data\event\\'.$triggeredevent, $event);
                $this->assertEquals(\context_module::instance($activity->cmid), $event->get_context());
                $this->assertEventContextNotUsed($event);
                $url = new \moodle_url('/mod/data/field.php', ['d' => $activity->id]);
                $this->assertEquals($url, $event->get_url());
            }
        }
    }
}
