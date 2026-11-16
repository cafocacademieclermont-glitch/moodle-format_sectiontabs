# Sections avec onglets (format_grettabs)

Format de cours Moodle qui reprend le format standard **Sections** (`format_topics`) et ajoute
au-dessus de chaque page du cours une **barre d'onglets automatique** donnant accès à toutes les sections,
dans l'esprit du format « une section par onglet ».

- La barre est construite automatiquement à partir des sections : rien à mettre à jour quand on ajoute,
  renomme, déplace ou masque une section.
- L'onglet de la section affichée est mis en évidence (couleur réglable).
- Les étudiants ne voient pas les sections qui leur sont cachées. Les enseignants les voient en grisé.
- Les sous-sections (activité « Sous-section ») ne sont pas affichées dans la barre.
- Le reste du cours fonctionne exactement comme le format Sections : index du cours, sous-sections,
  mise en évidence, édition, glisser-déposer…

## Compatibilité

Moodle 5.3 (testé sur 5.3.1). Le format standard **Sections** (`format_topics`) doit être présent (il l'est toujours, c'est un format du cœur de Moodle).

## Installation (administrateur)

1. Copier le dossier dans `public/course/format/grettabs` (le dossier doit s'appeler `grettabs`),
   ou installer le zip depuis *Administration du site → Plugins → Installer des plugins*.
2. Lancer la mise à jour depuis *Administration du site → Notifications*.
3. Facultatif : régler les couleurs dans *Administration du site → Plugins → Formats de cours → Sections avec onglets*.

## Utilisation (enseignant)

Dans *Paramètres du cours → Format de cours*, choisir **Sections avec onglets**. Deux options s'ajoutent :

| Option | Effet |
| --- | --- |
| L'accueil du cours n'affiche que la section générale | La page d'accueil du cours n'affiche que la section 0, chaque autre section s'ouvre sur sa propre page. En mode édition, la liste complète reste affichée pour pouvoir ajouter et déplacer les sections. |
| Noms d'onglets courts | « Bloc de compétence 1 : Actes essentiels » devient « Bloc de compétence 1 ». Le nom complet s'affiche au survol. |

Les sections et les activités sont conservées lors du changement de format.

## Personnalisation

L'apparence est définie dans `styles.css` (classes `.gtabs`, `.gtabs-tab`, `.gtabs-active`).
Le gabarit de la barre est `templates/local/tabs.mustache` et peut être surchargé par un thème
(`theme/MONTHEME/templates/format_grettabs/local/tabs.mustache`).

## Licence

GNU GPL v3 ou ultérieure.
