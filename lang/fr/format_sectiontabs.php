<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Chaînes en français.
 *
 * @package    format_sectiontabs
 * @copyright  2026 Eric Falcon
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['activeborder'] = 'Couleur du soulignement de l\'onglet actif';
$string['activeborder_desc'] = 'Couleur du trait sous l\'onglet actif.';
$string['activecolor'] = 'Couleur de l\'onglet actif';
$string['activecolor_desc'] = 'Couleur de fond par défaut de l\'onglet de la section affichée. Chaque cours peut définir sa propre couleur.';
$string['currentsection'] = 'Section en cours';
$string['gtabsactiveborder'] = 'Couleur du soulignement de l\'onglet actif';
$string['gtabsactiveborder_help'] = 'Couleur du trait sous l\'onglet actif, sous forme de code hexadécimal (par exemple #2b1a10). Laisser vide pour utiliser la couleur définie pour le site.';
$string['gtabsactivecolor'] = 'Couleur de l\'onglet actif';
$string['gtabsactivecolor_help'] = 'Couleur de fond de l\'onglet de la section affichée, sous forme de code hexadécimal (par exemple #ecb21e). Laisser vide pour utiliser la couleur définie pour le site.';
$string['gtabshome'] = 'L\'accueil du cours n\'affiche que la section générale';
$string['gtabshome_help'] = 'Si cette option est activée, la page d\'accueil du cours n\'affiche que la section générale, comme un format « une section par onglet ». Chaque autre section s\'affiche sur sa propre page. En mode édition, la liste complète des sections reste affichée pour pouvoir ajouter et déplacer des sections.';
$string['gtabsleftmenu'] = 'Menu des sections à gauche';
$string['gtabsleftmenu_help'] = 'Si cette option est activée, la liste des sections du cours s\'affiche à gauche du contenu, en plus de la barre d\'onglets. Elle est masquée sur les petits écrans, où la barre d\'onglets reste disponible.';
$string['gtabsmode'] = 'Onglets';
$string['gtabsmode_custom'] = 'Onglets personnalisés';
$string['gtabsmode_help'] = 'Un onglet par section : la barre d\'onglets reprend toutes les sections du cours.

Onglets personnalisés : vous définissez vous-même les onglets et les sections que chacun ouvre, sur la page « Onglets du cours » (lien ci-dessous, ou menu « Plus » du cours). Le menu de gauche continue de lister toutes les sections.';
$string['gtabsmode_sections'] = 'Un onglet par section';
$string['gtabsshortnames'] = 'Noms d\'onglets courts';
$string['gtabsshortnames_help'] = 'Si cette option est activée, seule la partie du nom de la section placée avant « : » sert de nom d\'onglet. Par exemple, « Bloc de compétence 1 : Actes essentiels » devient « Bloc de compétence 1 ». Le nom complet s\'affiche au survol de l\'onglet.';
$string['gtabssubtabs'] = 'Sous-sections en onglets';
$string['gtabssubtabs_help'] = 'Si cette option est activée, sur la page d\'une section, les sous-sections s\'affichent en onglets : seule la sous-section de l\'onglet choisi est affichée. En mode édition, un onglet « Tout afficher » permet de voir toutes les sous-sections pour les déplacer.';
$string['hiddenfromstudents'] = 'caché aux étudiants';
$string['hidefromothers'] = 'Cacher';
$string['invalidcolour'] = 'Saisissez un code couleur hexadécimal, par exemple #ecb21e.';
$string['managetabs'] = 'Onglets du cours';
$string['managetabs_info'] = '{$a} : définir les onglets et les sections qu\'ils ouvrent.';
$string['managetabs_intro'] = 'Un bloc par onglet. Donnez-lui un nom, puis choisissez les sections qu\'il ouvre, ou une adresse web. Un onglet peut regrouper plusieurs sections : il ouvre la première accessible et reste en surbrillance sur chacune d\'elles. Les blocs vides sont ignorés. Renommer une section ne casse pas les onglets.';
$string['managetabs_newcourse'] = 'Enregistrez d\'abord le cours, puis définissez les onglets depuis la page « Onglets du cours » (menu « Plus » du cours).';
$string['newsection'] = 'Nouvelle section';
$string['notavailable'] = 'non disponible';
$string['opensinnewwindow'] = 's\'ouvre dans une nouvelle fenêtre';
$string['page-course-view-sectiontabs'] = 'Toute page principale de cours au format Onglets et menu de sections';
$string['page-course-view-sectiontabs-x'] = 'Toute page de cours au format Onglets et menu de sections';
$string['plugin_description'] = 'Une section par page, avec une barre d\'onglets et un menu des sections donnant accès à chaque section, et les sous-sections affichées en onglets.';
$string['pluginname'] = 'Onglets et menu de sections';
$string['privacy:metadata'] = 'Le format Onglets et menu de sections n\'enregistre aucune donnée personnelle.';
$string['section0name'] = 'Généralités';
$string['sectionname'] = 'Section';
$string['showfromothers'] = 'Afficher';
$string['sidemenunav'] = 'Menu des sections du cours';
$string['subtabsnav'] = 'Sous-sections';
$string['subtabsshowall'] = 'Tout afficher';
$string['tabadd'] = 'Ajouter un onglet';
$string['tabdelete'] = 'Supprimer cet onglet';
$string['tablabel'] = 'Nom de l\'onglet';
$string['tablabel_required'] = 'Donnez un nom à cet onglet.';
$string['tabno'] = 'Onglet {no}';
$string['tabposition'] = 'Position';
$string['tabposition_help'] = 'Ordre de l\'onglet dans la barre : 1 pour le premier, 2 pour le deuxième… Des onglets avec la même position gardent l\'ordre de cette page.';
$string['tabsections'] = 'Sections';
$string['tabsections_help'] = 'Sections ouvertes par cet onglet. Avec plusieurs sections, l\'onglet ouvre la première accessible et reste en surbrillance sur chacune d\'elles. Un onglet ne s\'affiche pas pour un utilisateur qui ne peut voir aucune de ses sections.';
$string['tabsections_none'] = 'Aucune section choisie';
$string['tabsections_placeholder'] = 'Rechercher une section';
$string['tabsnav'] = 'Sections du cours';
$string['tabssaved'] = 'Les onglets du cours ont été enregistrés.';
$string['tabtarget_both'] = 'Choisissez soit des sections, soit une adresse web, pas les deux.';
$string['tabtarget_required'] = 'Choisissez au moins une section, ou saisissez une adresse web.';
$string['taburl'] = 'Ou adresse web';
$string['taburl_help'] = 'À la place de sections, l\'onglet peut ouvrir une adresse web (commençant par http:// ou https://). Elle s\'ouvre dans une nouvelle fenêtre.';
$string['taburl_invalid'] = 'Saisissez une adresse web commençant par http:// ou https://.';
