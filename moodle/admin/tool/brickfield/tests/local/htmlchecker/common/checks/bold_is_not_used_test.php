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
 * tool_brickfield check test.
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, https://www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_brickfield\local\htmlchecker\common\checks;

defined('MOODLE_INTERNAL') || die();

require_once('all_checks.php');

/**
 * Class bold_is_not_used_testcase
 */
final class bold_is_not_used_test extends all_checks {
    /** @var string Check type */
    protected $checktype = 'bold_is_not_used';

    /** @var string Html fail */
    private $htmlfail = <<<EOD
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN""http://www.w3.org/TR/REC-html40/loose.dtd">
    <html lang="en">
    <head>
    <title>b tags must not be used</title>
    </head>
    <body>
    <b>This text is bold</b>
    <b style="color: red">This text is also bold</b>
    </body>
    </html>
EOD;

    /** @var string Html pass */
    private $htmlpass = <<<EOD
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN""http://www.w3.org/TR/REC-html40/loose.dtd">
    <html lang="en">
    <head>
    <title>b tags must not be used</title>
    </head>
    <body>
    <strong>This text is strong</strong>
    </body>
    </html>
EOD;

    /**
     * Test for b tags being used
     */
    public function test_check() {
        $results = $this->get_checker_results($this->htmlfail);
        $this->assertTrue($results[0]->element->tagName == 'b');
        $this->assertTrue($results[1]->element->tagName == 'b');

        $results = $this->get_checker_results($this->htmlpass);
        $this->assertEmpty($results);
    }
}
