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
 * Privacy provider tests.
 *
 * @package    core_editor
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_editor\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\writer;
use core_editor\privacy\provider;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy provider tests class.
 *
 * @package    core_editor
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class provider_test extends \core_privacy\tests\provider_testcase {

    /**
     * When no preference exists, there should be no export.
     */
    public function test_no_preference() {
        global $USER;
        $this->resetAfterTest();
        $this->setAdminUser();

        provider::export_user_preferences($USER->id);
        $this->assertFalse(writer::with_context(\context_system::instance())->has_any_data());
    }

    /**
     * When preference exists but is empty, there should be no export.
     */
    public function test_empty_preference() {
        $this->resetAfterTest();

        // Create test user, add some preferences.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        set_user_preference('htmleditor', '', $user);

        // Switch to admin user (so we can validate preferences of the correct user are being exported).
        $this->setAdminUser();

        // Export test users preferences.
        provider::export_user_preferences($user->id);
        $this->assertFalse(writer::with_context(\context_system::instance())->has_any_data());
    }

    /**
     * When an editor is set, the name of that editor will be reported.
     */
    public function test_editor_atto() {
        $this->resetAfterTest();

        // Create test user, add some preferences.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        set_user_preference('htmleditor', 'atto');

        // Switch to admin user (so we can validate preferences of the correct user are being exported).
        $this->setAdminUser();

        // Export test users preferences.
        provider::export_user_preferences($user->id);
        $this->assertTrue(writer::with_context(\context_system::instance())->has_any_data());

        $prefs = writer::with_context(\context_system::instance())->get_user_preferences('core_editor');
        $this->assertNotEmpty($prefs->htmleditor);
        $this->assertNotEmpty($prefs->htmleditor->value);
        $this->assertNotEmpty($prefs->htmleditor->description);
        $this->assertEquals('atto', $prefs->htmleditor->value);

        $this->assertEquals(
            get_string(
                'privacy:preference:htmleditor',
                'core_editor',
                get_string('pluginname', "editor_atto")
            ),
            $prefs->htmleditor->description
        );
    }
}
