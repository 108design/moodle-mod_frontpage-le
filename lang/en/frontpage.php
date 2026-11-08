<?php
/**
 * Course activity interface.
 *
 * @package mod_frontpage
 * @copyright 2026 Andreas Giesen <andreas@108design.com>
 * @license 108design source-available software license; see LICENSE.md
 */

$string['pluginname'] = 'Frontpage Course Activity';
$string['modulename'] = 'Frontpage Course Activity';
$string['modulenameplural'] = 'Frontpage Course Activities';
$string['pluginadministration'] = 'Frontpage administration';
$string['frontpagename'] = 'Activity name';
$string['editcontent'] = 'Edit content';
$string['completionallactivities'] = 'Require all activities and resources included on this Frontpage to be completed';
$string['completiondetail:allactivities'] = 'Complete all included activities and resources that use activity completion';
$string['frontpage:addinstance'] = 'Add a Frontpage activity';
$string['frontpage:view'] = 'View a Frontpage activity';
$string['frontpage:managecontent'] = 'Manage Frontpage activity content';
$string['frontpage:publishcontent'] = 'Publish activity content';
$string['privacy:metadata'] = ('Page content and author attribution belong to local_frontpage. Moodle manages '
    . 'course access, completion and activity introductions.');
$string['notpublished'] = 'This page has not been published yet.';
$string['missingreference'] = 'Select an activity available in this course.';
$string['completionallactivities_help'] = ('The optional All Referenced Activities rule uses enabled references in the '
    . 'current publication. References in a disabled Grid are excluded. Activities '
    . 'without completion tracking are skipped. At least one tracked activity is '
    . 'required. Missing or deleted references keep the rule incomplete. Hidden or '
    . 'temporarily unavailable tracked activities still use their native completion '
    . 'state. Completion with pass or fail counts as completed. Historical previews '
    . 'do not award completion.');

$string['frontpage:editcss'] = 'Edit CSS for this Frontpage activity';

// Shared independent signed update protection.
$string['updateguard_heading'] = 'Update stopped';
$string['updateguard_component'] = 'The package for {$a} was rejected.';
$string['unsupportedupgrade'] = 'This package cannot update the installed version. Restore the previous plugin files.';
$string['updateguard_hint'] = 'Use an approved package for the installed edition. You can review the operation again.';
$string['updateguard_unchanged'] = 'This request did not replace plugin files.';
$string['updateguard_help'] = 'The installed plugin checks the publisher signature, package contents, permitted update channel and compatibility before replacement. A valid signature does not authorise a different edition. If one package is rejected, the whole batch stops.';
$string['updateguard_signature'] = 'The publisher signature or package contents could not be verified.';
$string['updateguard_policy'] = 'This package does not match the installed edition or permitted package profile.';
$string['updateguard_channel'] = 'Updates through this channel are not enabled for this plugin.';
$string['updateguard_metadata'] = 'The version or dependency information is inconsistent.';
$string['updateguard_dependency'] = 'A required plugin version is missing. Update the dependency together with this plugin or beforehand.';
$string['updateguard_approval'] = 'The packages or installed state have changed since the preview. Review the operation again.';
$string['updateguard_busy'] = 'Another installation is running. Try again after it finishes.';
$string['updateguard_installed'] = 'The installed update protection is incomplete. Restore the files of the last approved installed version.';
$string['updateguard_operation'] = 'This combination of installation actions is not supported. Start a single update operation again.';
$string['updateguard_failed'] = 'The update could not be verified. No installation was started.';
$string['updateguard_back'] = 'Return to plugin overview';
// End shared independent signed update protection.
