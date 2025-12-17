# Composant Office Switch

## Description
Le composant `office-switch.php` permet de basculer facilement entre le Front Office et le Back Office sans nécessiter de connexion ou d'authentification.

## Utilisation

### 1. Inclure le composant
```php
include_once(__DIR__ . '/../components/office-switch.php');
```

### 2. Afficher le bouton switch
```php
// Pour les pages de liste
echo renderOfficeSwitch('front', 'projet');
echo renderOfficeSwitch('back', 'actualite');

// Pour les pages de détails avec ID
echo renderOfficeSwitch('front', 'projet', $projet_id);
```

## Paramètres

- **$currentOffice** : `'front'` ou `'back'` - Indique l'office actuel
- **$section** : `'projet'` ou `'actualite'` - Indique la section
- **$id** : (optionnel) ID de l'élément pour les pages de détails

## Pages intégrées

### Front Office
- ✅ listeprojet.php
- ✅ detailsprojet.php
- ✅ ajoutprojet.php
- ✅ modifierprojet.php
- ✅ listeActualite.php
- ✅ ajouterActualite.php
- ✅ searchActualites.php

### Back Office
- ✅ projet/listeProjet.php
- ✅ projet/ajouterProjet.php
- ✅ projet/modifierProjet.php
- ✅ actualite/listeActualite.php
- ✅ actualite/ajouterActualite.php
- ✅ actualite/modifierActualite.php
- ✅ actualite/searchActualites.php

## Style
Le bouton est positionné en haut à droite de l'écran (position fixe) avec :
- Design moderne avec switch animé
- Couleurs : Bleu/Violet (gradient)
- Icône de basculement
- Responsive et accessible
