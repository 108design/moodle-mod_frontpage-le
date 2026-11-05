<?php
/**
 * Regression tests for restore reference mapper.
 *
 * @package mod_frontpage
 * @copyright 2026 Andreas Giesen <andreas@108design.com>
 * @license 108design source-available software license; see LICENSE.md
 */

namespace mod_frontpage;

/**
 * Regression tests for restore reference mapper.
 */
final class restore_reference_mapper_test extends \PHPUnit\Framework\TestCase {
    /**
     * Verify that restore maps course module IDs and preserves segment ID.
     *
     * @return void
     */
    public function test_restore_maps_course_module_ids_and_preserves_segment_id(): void {
        $doc = [
        'schemaVersion' => 2,
         'segments' => [ [ 'id' => 'resource', 'type' => 'course_module', 'props' => [ 'cmid' => 15 ] ] ],
        ];
        $result = json_decode(
            \mod_frontpage\local\restore_reference_mapper::document(
                json_encode($doc),
                fn(
                    $type,
                    $id
                )=>$id === 15 ? 24 : null
            ),
            true
        );
        self::assertSame('resource', $result['segments'][0]['id']);
        self::assertSame(24, $result['segments'][0]['props']['cmid']);
    }
    /**
     * Verify that missing reference is an explicit placeholder.
     *
     * @return void
     */
    public function test_missing_reference_is_an_explicit_placeholder(): void {
        $doc = [
        'schemaVersion' => 2,
         'segments' => [ [ 'id' => 'resource', 'type' => 'course_module', 'props' => [ 'cmid' => 15 ] ] ],
        ];
        $result = json_decode(
            \mod_frontpage\local\restore_reference_mapper::document(json_encode($doc), fn()=>null),
            true
        );
        self::assertTrue($result['segments'][0]['props']['placeholder']);
        self::assertSame(0, $result['segments'][0]['props']['cmid']);
    }
}
