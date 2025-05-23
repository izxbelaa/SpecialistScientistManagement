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

namespace mod_bigbluebuttonbn\local\proxy;

use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\test\testcase_helper_trait;

/**
 * Recording proxy tests class.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2018 - present, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 * @covers  \mod_bigbluebuttonbn\local\proxy\recording_proxy
 * @coversDefaultClass \mod_bigbluebuttonbn\local\proxy\recording_proxy
 */
final class recording_proxy_test extends \advanced_testcase {
    use testcase_helper_trait;

    /**
     * Simple recording fetcher test
     *
     * @return void
     */
    public function test_fetch_recordings() {
        $this->resetAfterTest();
        $this->initialise_mock_server();
        [$context, $cm, $bbbactivity] = $this->create_instance();
        $instance = instance::get_from_instanceid($bbbactivity->id);
        $recordings = $this->create_recordings_for_instance($instance, [['name' => 'Recording 1'], ['name' => 'Recording 2']]);
        $recordingsid = array_map(function ($r) {
            return $r->recordingid;
        }, $recordings);
        $recordings = recording_proxy::fetch_recordings($recordingsid);
        $this->assertCount(2, $recordings);
    }

    /**
     * Simple recording with breakoutroom fetcher test
     *
     * @return void
     */
    public function test_fetch_recordings_breakoutroom() {
        $this->resetAfterTest();
        $this->initialise_mock_server();
        [$context, $cm, $bbbactivity] = $this->create_instance();
        $instance = instance::get_from_instanceid($bbbactivity->id);
        $bbbgenerator = $this->getDataGenerator()->get_plugin_generator('mod_bigbluebuttonbn');
        $mainmeeting = $bbbgenerator->create_meeting([
            'instanceid' => $instance->get_instance_id(),
            'groupid' => $instance->get_group_id(),
        ]);
        // TODO: here we artificially create breakout meetings but the current implementations does not handle
        // breakout rooms for one BBB instance. At this point we just have the ability to retrieve subrecordings
        // from breakout rooms and manage them as if they belong to the parent recording.
        // The meetingId is not sent to the server but autogenerated by the mock server and
        // parentID is the meetingID from the current instance.
        $submeeting1 = $bbbgenerator->create_meeting([
            'instanceid' => $instance->get_instance_id(),
            'groupid' => $instance->get_group_id(),
            'isBreakout' => true,
            'sequence' => 1
        ]);
        $submeeting2 = $bbbgenerator->create_meeting([
            'instanceid' => $instance->get_instance_id(),
            'groupid' => $instance->get_group_id(),
            'isBreakout' => true,
            'sequence' => 2
        ]);
        $recordings = $this->create_recordings_for_instance($instance, [
            ['name' => 'Recording 1'],
            ['name' => 'Recording 2', 'isBreakout' => true, 'sequence' => 1],
            ['name' => 'Recording 3', 'isBreakout' => true, 'sequence' => 2]
        ]);
        $recordingsid = array_map(function ($r) {
            return $r->recordingid;
        }, $recordings);
        $recordings = recording_proxy::fetch_recordings([$recordingsid[0]]);
        $this->assertCount(3, $recordings);
    }
}
