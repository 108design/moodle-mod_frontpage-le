<?php
/**
 * Course activity interface.
 *
 * @package mod_frontpage
 * @copyright 2026 Andreas Giesen <andreas@108design.com>
 * @license 108design source-available software license; see LICENSE.md
 */

$string['pluginname'] = 'Frontpage-Kursaktivität';
$string['modulename'] = 'Frontpage Kursaktivität';
$string['modulenameplural'] = 'Frontpage Kursaktivitäten';
$string['frontpagename'] = 'Name der Aktivität';
$string['editcontent'] = 'Inhalt bearbeiten';
$string['completionallactivities'] = 'Alle auf dieser Frontpage eingebundenen Aktivitäten und Materialien müssen abgeschlossen sein';
$string['completiondetail:allactivities'] = 'Alle eingebundenen Aktivitäten und Materialien mit Abschlussverfolgung abschließen';
$string['frontpage:addinstance'] = 'Frontpage-Aktivität hinzufügen';
$string['frontpage:view'] = 'Frontpage-Aktivität anzeigen';
$string['frontpage:managecontent'] = 'Inhalt einer Frontpage-Aktivität verwalten';
$string['frontpage:publishcontent'] = 'Aktivitätsinhalt veröffentlichen';
$string['privacy:metadata'] = ('Seiteninhalt und Zuordnung der Bearbeiter gehören zu local_frontpage. Moodle '
    . 'verwaltet Kurszugang, Abschluss und Aktivitätsbeschreibungen.');
$string['notpublished'] = 'Diese Seite wurde noch nicht veröffentlicht.';
$string['missingreference'] = 'Wähle eine in diesem Kurs verfügbare Aktivität.';
$string['completionallactivities_help'] = ('Die optionale Regel Alle referenzierten Aktivitäten verwendet aktive '
    . 'Referenzen der aktuellen Veröffentlichung. Referenzen in einem deaktivierten '
    . 'Grid werden nicht berücksichtigt. Aktivitäten ohne Abschlussverfolgung '
    . 'werden übersprungen. Mindestens eine verfolgte Aktivität ist erforderlich. '
    . 'Fehlende oder gelöschte Referenzen halten die Regel unerfüllt. '
    . 'Ausgeblendete oder zeitweise nicht verfügbare verfolgte Aktivitäten '
    . 'verwenden weiterhin ihren nativen Abschlusszustand. Auch ein Abschluss mit '
    . 'bestanden oder nicht bestanden zählt als abgeschlossen. Historische '
    . 'Vorschauen vergeben keinen Abschluss.');

$string['frontpage:editcss'] = 'CSS dieser Frontpage-Aktivität bearbeiten';

// Shared independent signed update protection.
$string['updateguard_heading'] = 'Update angehalten';
$string['updateguard_component'] = 'Das Paket für {$a} wurde abgewiesen.';
$string['updateguard_hint'] = 'Verwende ein freigegebenes Paket für die installierte Ausgabe. Du kannst den Vorgang erneut prüfen.';
$string['updateguard_unchanged'] = 'Dieser Vorgang hat keine Plugin-Dateien ersetzt.';
$string['updateguard_help'] = 'Das installierte Plugin prüft vor dem Ersetzen die Herausgebersignatur, den Paketinhalt, den erlaubten Updatekanal und die Kompatibilität. Eine gültige Signatur erlaubt keinen Wechsel auf eine andere Ausgabe. Wird ein Paket abgewiesen, stoppt die gesamte Sammelaktion.';
$string['updateguard_signature'] = 'Die Herausgebersignatur oder der Paketinhalt konnte nicht bestätigt werden.';
$string['updateguard_policy'] = 'Das Paket passt nicht zur installierten Ausgabe oder zum erlaubten Paketprofil.';
$string['updateguard_channel'] = 'Updates über diesen Kanal sind für dieses Plugin nicht freigegeben.';
$string['updateguard_metadata'] = 'Die Versions- oder Abhängigkeitsangaben sind nicht konsistent.';
$string['updateguard_dependency'] = 'Eine benötigte Plugin-Version fehlt. Aktualisiere die Abhängigkeit gemeinsam mit diesem Plugin oder vorher.';
$string['updateguard_approval'] = 'Die Pakete oder der installierte Stand haben sich seit der Vorschau geändert. Prüfe den Vorgang erneut.';
$string['updateguard_busy'] = 'Eine andere Installation läuft. Versuche es nach deren Abschluss erneut.';
$string['updateguard_installed'] = 'Der installierte Updateschutz ist unvollständig. Stelle die Dateien der zuletzt installierten freigegebenen Version wieder her.';
$string['updateguard_operation'] = 'Diese Kombination von Installationsaktionen wird nicht unterstützt. Starte einen einzelnen Updatevorgang erneut.';
$string['updateguard_failed'] = 'Das Update konnte nicht geprüft werden. Es wurde keine Installation gestartet.';
$string['updateguard_back'] = 'Zur Pluginübersicht';
// End shared independent signed update protection.
