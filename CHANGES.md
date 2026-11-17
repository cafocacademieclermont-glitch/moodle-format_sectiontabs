# Historique des versions

## 1.0 (stable) — 17 novembre 2026

Première version stable du format **Onglets et menu de sections** (`format_sectiontabs`) pour Moodle 5.3.

- Barre d'onglets au-dessus de chaque page : un onglet par section, ou **onglets personnalisés** réglés dans les
  paramètres du cours (nom, une ou plusieurs sections, ou adresse web).
- **Menu des sections à gauche** du contenu.
- **Sous-sections en onglets** à l'intérieur d'une section, avec un onglet « Tout afficher » en mode édition.
- **Boutons Précédent / Suivant** : sous-section précédente / suivante, puis section précédente / suivante.
- Couleur de l'onglet actif réglable pour le site et pour chaque cours ; couleur du texte choisie automatiquement
  pour un contraste suffisant.
- Compatible **mode clair et mode sombre** (variables de couleur de Bootstrap).
- Respect des sections cachées et des restrictions d'accès.
- Mode édition : bouton « Ajouter une section » sur les pages de section ; ouverture de l'onglet d'une sous-section
  après son ajout ou sa modification.
- Onglets personnalisés conservés lors d'une sauvegarde / restauration, duplication ou importation du cours.
- Accessibilité vérifiée (WCAG 2.1 / 2.2 AA, clavier, lecteurs d'écran) ; revue de sécurité ; code conforme à
  moodle-cs.

Versions de développement (0.1 à 0.9) : publiées d'abord sous le nom `format_grettabs`, puis `format_sectiontabs`.
