<?php
/**
 * Remap course-module references inside restored page documents.
 *
 * @package mod_frontpage
 * @copyright 2026 Andreas Giesen <andreas@108design.com>
 * @license 108design source-available software license; see LICENSE.md
 */

namespace mod_frontpage\local;

/**
 * Remap course-module references inside restored page documents.
 */
final class restore_reference_mapper {
    /**
     * Remap restored native activity references in the page document.
     *
     * @param string $json Encoded JSON document.
     * @param callable $mapper Mapper.
     * @return string|null
     */
    public static function document(string $json, callable $mapper): ?string {
        $doc = json_decode($json, true);
        if (!is_array($doc) || !is_array($doc['segments'] ?? null)) {
            return null;
        }
        foreach ($doc['segments'] as &$segment) {
            if (($segment['type'] ?? '') !== 'course_module' || !empty($segment['props']['placeholder'])) {
                continue;
            }
            $mapped = $mapper('course_module', (int)($segment['props']['cmid'] ?? 0));
            if ($mapped) {
                $segment['props']['cmid'] = $mapped;
            } else {
                $segment['props']['cmid'] = 0;
                $segment['props']['placeholder'] = true;
                $segment['props']['placeholderlabel'] = get_string('missingreference', 'mod_frontpage');
            }
        }
        unset($segment);
        return json_encode($doc, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
