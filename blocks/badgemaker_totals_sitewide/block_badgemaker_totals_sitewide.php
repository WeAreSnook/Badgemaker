<?php

/**
 * @package    BadgeMaker
 * @copyright  2017 We Are Snook <code@wearesnook.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 */

global $CFG;

require_once($CFG->libdir . "/badgeslib.php");
require_once($CFG->dirroot . '/local/badgemaker/lib.php');
require_once($CFG->dirroot . "/local/badgemaker/renderer.php");

class block_badgemaker_totals_sitewide extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_badgemaker_totals_sitewide');
    }

    public function instance_allow_multiple() {
        return false;
    }

    public function has_config() {
        return false;
    }

    public function instance_allow_config() {
        return true;
    }

    public function applicable_formats() {
        return array(
                'admin' => true,
                'site-index' => true,
                'course-view' => true,
                'mod' => true,
                'my' => true
        );
    }

    public function specialization() {
        if (empty($this->config->title)) {
            $this->title = get_string('title', 'block_badgemaker_totals_sitewide');
        } else {
            $this->title = $this->config->title;
        }
    }

    // function preferred_width() {
    //   return 1000;
    // }

    public function get_content() {
        global $USER, $PAGE, $CFG;

        if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->config)) {
            $this->config = new stdClass();
        }

        // Create empty content.
        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->text .= html_writer::start_span('lead');

        if (empty($CFG->enablebadges)) {
            $this->content->text .= get_string('badgesdisabled', 'badges');
            return $this->content;
        }

        $siteBadges = badges_get_badges(BADGE_TYPE_SITE);
        $courseBadges = badges_get_badges(BADGE_TYPE_COURSE);
        $totalBadges = count($siteBadges) + count($courseBadges);
        $issuedCount = local_badgemaker_issuedBadgesCount();

        $this->content->text .= "<b>";
        $blurl = local_badgemaker_libraryPageURL();
        $this->content->text .= html_writer::link($blurl, ''.$issuedCount);
        $this->content->text .= "</b>" . get_string('awarded', 'block_badgemaker_totals_sitewide');
        $this->content->text .= '<br>' . "<b>";
	$mlurl = local_badgemaker_libraryAllPageURL();
        $this->content->text .= html_writer::link($mlurl, ''.$totalBadges);
        $this->content->text .= '</b>' . get_string('all_badges', 'block_badgemaker_totals_sitewide');
        $this->content->text .= html_writer::end_span();

        // $foot = get_string('footer', 'block_badgemaker_totals_sitewide');
        // if (strlen($foot) > 0) {
        //   $this->content->footer = html_writer::link($blurl, $foot);
        // }

        $bmOutput = new badgemaker_renderer($this->page, 'badges');
        $this->content->text .= $bmOutput->print_badgemaker_linked_logo();

        return $this->content;
    }
}
