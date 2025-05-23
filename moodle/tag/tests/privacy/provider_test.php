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
 * Privacy tests for core_tag.
 *
 * @package    core_comment
 * @category   test
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_tag\privacy;

defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->dirroot . '/tag/lib.php');

use core_privacy\tests\provider_testcase;
use core_privacy\local\request\writer;
use core_tag\privacy\provider;
use core_privacy\local\request\approved_userlist;

/**
 * Unit tests for tag/classes/privacy/policy
 *
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class provider_test extends provider_testcase {

    /**
     * Check the exporting of tags for a user id in a context.
     */
    public function test_export_tags() {
        global $DB;

        $this->resetAfterTest(true);

        // Create a user to perform tagging.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Create a course to tag.
        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);
        $subcontext = [];

        // Create three dummy tags and tag instances.
        $dummytags = [ 'Tag 1', 'Tag 2', 'Tag 3' ];
        \core_tag_tag::set_item_tags('core_course', 'course', $course->id, \context_course::instance($course->id),
                                    $dummytags, $user->id);

        // Get the tag instances that should have been created.
        $taginstances = $DB->get_records('tag_instance', array('itemtype' => 'course', 'itemid' => $course->id));
        $this->assertCount(count($dummytags), $taginstances);

        // Check tag instances match the component and context.
        foreach ($taginstances as $taginstance) {
            $this->assertEquals('core_course', $taginstance->component);
            $this->assertEquals(\context_course::instance($course->id)->id, $taginstance->contextid);
        }

        // Retrieve tags only for this user.
        provider::export_item_tags($user->id, $context, $subcontext, 'core_course', 'course', $course->id, true);

        $writer = writer::with_context($context);
        $this->assertTrue($writer->has_any_data());

        $exportedtags = $writer->get_related_data($subcontext, 'tags');
        $this->assertCount(count($dummytags), $exportedtags);

        // Check the exported tag's rawname is found in the initial dummy tags.
        foreach ($exportedtags as $exportedtag) {
            $this->assertContains($exportedtag, $dummytags);
        }
    }

    /**
     * Test method delete_item_tags().
     */
    public function test_delete_item_tags() {
        global $DB;

        $this->resetAfterTest(true);

        // Create a course to tag.
        $course1 = $this->getDataGenerator()->create_course();
        $context1 = \context_course::instance($course1->id);
        $course2 = $this->getDataGenerator()->create_course();
        $context2 = \context_course::instance($course2->id);

        // Tag courses.
        \core_tag_tag::set_item_tags('core_course', 'course', $course1->id, $context1, ['Tag 1', 'Tag 2', 'Tag 3']);
        \core_tag_tag::set_item_tags('core_course', 'course', $course2->id, $context2, ['Tag 1', 'Tag 2']);

        $expectedtagcount = $DB->count_records('tag_instance');
        // Delete tags for course1.
        provider::delete_item_tags($context1, 'core_course', 'course');
        $expectedtagcount -= 3;
        $this->assertEquals($expectedtagcount, $DB->count_records('tag_instance'));

        // Delete tags for course2. Use wrong itemid.
        provider::delete_item_tags($context2, 'core_course', 'course', $course1->id);
        $this->assertEquals($expectedtagcount, $DB->count_records('tag_instance'));

        // Use correct itemid.
        provider::delete_item_tags($context2, 'core_course', 'course', $course2->id);
        $expectedtagcount -= 2;
        $this->assertEquals($expectedtagcount, $DB->count_records('tag_instance'));
    }

    /**
     * Test method delete_item_tags() with userid.
     */
    public function test_delete_item_tags_with_userid() {
        global $DB;

        $this->resetAfterTest(true);
        // Create a course to tag.
        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);

        // Create a user to perform tagging.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Tag courses.
        \core_tag_tag::set_item_tags('core_course', 'course', $course->id, $context, ['Tag 1', 'Tag 2'], $user->id);
        $expectedtagcount = $DB->count_records('tag_instance');

        // Delete tags for course. Use wrong userid.
        provider::delete_item_tags($context, 'core_course', 'course', null, 1);
        $this->assertEquals($expectedtagcount, $DB->count_records('tag_instance'));

        $expectedtagcount -= 2;
        // Delete tags for course. Use correct userid.
        provider::delete_item_tags($context, 'core_course', 'course', null, $user->id);
        $this->assertEquals($expectedtagcount, $DB->count_records('tag_instance'));
    }

    /**
     * Test method delete_item_tags_select().
     */
    public function test_delete_item_tags_select() {
        global $DB;

        $this->resetAfterTest(true);

        // Create a course to tag.
        $course1 = $this->getDataGenerator()->create_course();
        $context1 = \context_course::instance($course1->id);
        $course2 = $this->getDataGenerator()->create_course();
        $context2 = \context_course::instance($course2->id);

        // Tag courses.
        \core_tag_tag::set_item_tags('core_course', 'course', $course1->id, $context1, ['Tag 1', 'Tag 2', 'Tag 3']);
        \core_tag_tag::set_item_tags('core_course', 'course', $course2->id, $context2, ['Tag 1', 'Tag 2']);

        $expectedtagcount = $DB->count_records('tag_instance');
        // Delete tags for course1.
        list($sql, $params) = $DB->get_in_or_equal([$course1->id, $course2->id], SQL_PARAMS_NAMED);
        provider::delete_item_tags_select($context1, 'core_course', 'course', $sql, $params);
        $expectedtagcount -= 3;
        $this->assertEquals($expectedtagcount, $DB->count_records('tag_instance'));

        // Delete tags for course2.
        provider::delete_item_tags_select($context2, 'core_course', 'course', $sql, $params);
        $expectedtagcount -= 2;
        $this->assertEquals($expectedtagcount, $DB->count_records('tag_instance'));
    }

    protected function set_up_tags() {
        global $CFG;
        require_once($CFG->dirroot.'/user/editlib.php');

        $this->resetAfterTest(true);

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $this->setUser($user1);
        useredit_update_interests($user1, ['Birdwatching', 'Computers']);

        $this->setUser($user2);
        useredit_update_interests($user2, ['computers']);

        $this->setAdminUser();

        $tag = \core_tag_tag::get_by_name(0, 'computers', '*');
        $tag->update(['description' => '<img src="@@PLUGINFILE@@/computer.jpg">']);
        get_file_storage()->create_file_from_string([
            'contextid' => \context_system::instance()->id,
            'component' => 'tag',
            'filearea' => 'description',
            'itemid' => $tag->id,
            'filepath' => '/',
            'filename' => 'computer.jpg'
        ], "jpg:image");

        return [$user1, $user2];
    }

    public function test_export_item_tags() {
        list($user1, $user2) = $this->set_up_tags();
        $this->assertEquals([\context_system::instance()->id],
            provider::get_contexts_for_userid($user1->id)->get_contextids());
        $this->assertEmpty(provider::get_contexts_for_userid($user2->id)->get_contextids());
    }

    public function test_delete_data_for_user() {
        global $DB;
        list($user1, $user2) = $this->set_up_tags();
        $context = \context_system::instance();
        $this->assertEquals(2, $DB->count_records('tag', []));
        $this->assertEquals(0, $DB->count_records('tag', ['userid' => 0]));
        provider::delete_data_for_user(new \core_privacy\local\request\approved_contextlist($user2, 'core_tag', [$context->id]));
        $this->assertEquals(2, $DB->count_records('tag', []));
        $this->assertEquals(0, $DB->count_records('tag', ['userid' => 0]));
        provider::delete_data_for_user(new \core_privacy\local\request\approved_contextlist($user1, 'core_tag', [$context->id]));
        $this->assertEquals(2, $DB->count_records('tag', []));
        $this->assertEquals(2, $DB->count_records('tag', ['userid' => 0]));
    }

    public function test_delete_data_for_all_users_in_context() {
        global $DB;
        $course = $this->getDataGenerator()->create_course();
        list($user1, $user2) = $this->set_up_tags();
        $this->assertEquals(2, $DB->count_records('tag', []));
        $this->assertEquals(3, $DB->count_records('tag_instance', []));
        provider::delete_data_for_all_users_in_context(\context_course::instance($course->id));
        $this->assertEquals(2, $DB->count_records('tag', []));
        $this->assertEquals(3, $DB->count_records('tag_instance', []));
        provider::delete_data_for_all_users_in_context(\context_system::instance());
        $this->assertEquals(0, $DB->count_records('tag', []));
        $this->assertEquals(0, $DB->count_records('tag_instance', []));
    }

    public function test_export_data_for_user() {
        global $DB;
        list($user1, $user2) = $this->set_up_tags();
        $context = \context_system::instance();
        provider::export_user_data(new \core_privacy\local\request\approved_contextlist($user2, 'core_tag', [$context->id]));
        $this->assertFalse(writer::with_context($context)->has_any_data());

        $tagids = array_values(array_map(function($tag) {
            return $tag->id;
        }, \core_tag_tag::get_by_name_bulk(\core_tag_collection::get_default(), ['Birdwatching', 'Computers'])));

        provider::export_user_data(new \core_privacy\local\request\approved_contextlist($user1, 'core_tag', [$context->id]));
        $writer = writer::with_context($context);

        $data = $writer->get_data(['Tags', $tagids[0]]);
        $files = $writer->get_files(['Tags', $tagids[0]]);
        $this->assertEquals('Birdwatching', $data->rawname);
        $this->assertEmpty($files);

        $data = $writer->get_data(['Tags', $tagids[1]]);
        $files = $writer->get_files(['Tags', $tagids[1]]);
        $this->assertEquals('Computers', $data->rawname);
        $this->assertEquals(['computer.jpg'], array_keys($files));
    }

    /**
     * Test that only users within a system context are fetched.
     */
    public function test_get_users_in_context() {
        $component = 'core_tag';

        $user1 = $this->set_up_tags()[0];
        $systemcontext = \context_system::instance();

        $userlist1 = new \core_privacy\local\request\userlist($systemcontext, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(1, $userlist1);
        $expected = [$user1->id];
        $actual = $userlist1->get_userids();
        $this->assertEquals($expected, $actual);

        // The list of users within the a context other than system context should be empty.
        $usercontext1 = \context_user::instance($user1->id);
        $userlist2 = new \core_privacy\local\request\userlist($usercontext1, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(0, $userlist2);
    }

    /**
     * Test that data for users in approved userlist is deleted.
     */
    public function test_delete_data_for_users() {
        $component = 'core_tag';

        list($user1, $user2) = $this->set_up_tags();
        $usercontext1 = \context_user::instance($user1->id);
        $user3 = $this->getDataGenerator()->create_user();
        $systemcontext = \context_system::instance();

        $this->setUser($user2);
        useredit_update_interests($user2, ['basketball']);

        $this->setUser($user3);
        useredit_update_interests($user3, ['soccer']);

        $userlist = new \core_privacy\local\request\userlist($systemcontext, $component);
        provider::get_users_in_context($userlist);
        $this->assertCount(3, $userlist);
        $this->assertTrue(in_array($user1->id, $userlist->get_userids()));
        $this->assertTrue(in_array($user2->id, $userlist->get_userids()));
        $this->assertTrue(in_array($user3->id, $userlist->get_userids()));

        // Data should not be deleted in contexts other than system context.
        // Convert $userlist into an approved_contextlist.
        $approvedlist = new approved_userlist($usercontext1, $component, $userlist->get_userids());
        // Delete using delete_data_for_user.
        provider::delete_data_for_users($approvedlist);
        // Re-fetch users in systemcontext.
        $userlist = new \core_privacy\local\request\userlist($systemcontext, $component);
        provider::get_users_in_context($userlist);
        // The user data in systemcontext should not be deleted.
        $this->assertCount(3, $userlist);

        // Add user1 and user2 into an approved_contextlist.
        $approvedlist = new approved_userlist($systemcontext, $component, [$user1->id, $user2->id]);
        // Delete using delete_data_for_user.
        provider::delete_data_for_users($approvedlist);
        // Re-fetch users in systemcontext.
        $userlist = new \core_privacy\local\request\userlist($systemcontext, $component);
        provider::get_users_in_context($userlist);
        // The approved user data in systemcontext should be deleted.
        // The user list should return user3.
        $this->assertCount(1, $userlist);
        $this->assertTrue(in_array($user3->id, $userlist->get_userids()));
    }
}
