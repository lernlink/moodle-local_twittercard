<?php
// This file is part of the Twitter Card local plugin for Moodle
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
 * Tests for the helper used to create a Twitter summary card.
 *
 * @package    local_twittercard
 * @copyright  2017 Matteo Scaramuccia <moodle@matteoscaramuccia.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class local_twittercard_helper_testcase
 *
 * @package    local_twittercard
 * @copyright  2017 Matteo Scaramuccia <moodle@matteoscaramuccia.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_twittercard_helper_testcase extends advanced_testcase {
    /**
     * Test set up.
     */
    public function setUp() {
        $this->resetAfterTest();
    }

    /**
     * This are the tests for creating Twitter summary cards, even when using multi-language content.
     */
    public function test_create_card() {
        // Create a course.
        $course = $this->getDataGenerator()->create_course(array(
            'fullname' => 'title',
            'summary' => 'description'
        ));
        $context = context_course::instance($course->id);

        $this->assertEquals(
            "<meta name='twitter:card' content='summary' />\n" .
                "<meta name='twitter:title' content='title' />\n" .
                "<meta name='twitter:description' content='description' />\n",
            \local_twittercard\helper::create_card($context, $course));

        // Create another course.
        $course = $this->getDataGenerator()->create_course(array(
            'fullname' => '<span lang="en" class="multilang">English</span>' .
                '<span lang="fr" class="multilang">Italian</span> title',
            'summary' => '<span lang="en" class="multilang">English</span>' .
                '<span lang="fr" class="multilang">Italian</span> description'
        ));
        $context = context_course::instance($course->id);
        filter_manager::reset_caches();
        // Enable the multilang filter and set it to apply to headings and content.
        filter_set_global_state('multilang', TEXTFILTER_ON);
        filter_set_applies_to_strings('multilang', true);

        $this->assertEquals(
            "<meta name='twitter:card' content='summary' />\n" .
                "<meta name='twitter:title' content='English title' />\n" .
                "<meta name='twitter:description' content='English description' />\n",
            \local_twittercard\helper::create_card($context, $course));
    }
}
