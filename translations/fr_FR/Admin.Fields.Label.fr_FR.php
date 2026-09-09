<?php

return array (
  'label' =>
  array (
    'default' =>
    array (
      'id' => '#',
      'active' => 'Actif',
      'dateAdd' => 'Date',
      'status' => 'Statut',
      'comment' => 'Commentaire',
      'position' => 'Position',
      'yes' => 'Oui',
      'no' => 'Non',
    ),
    'user' =>
    array (
      'email' => 'Email',
      'lastname' => 'Nom',
      'firstname' => 'Prénom',
      'role' => 'Role',
      'password' => 'Mot de passe',
      'passwordRepeat' => 'Confirmation Mot de passe',
    ),
    'role' =>
    array (
      'name' => 'Nom',
      'code' => 'Code',
      'superAdmin' => 'Accès complet',
      'authorization' => 'Permissions',
      'entity' => 'Section',
      'allEntities' => 'Toutes les sections',
      'toggleColumn' => 'Cocher ou décocher toute la colonne',
      'permission' =>
      array (
        'view' => 'Voir',
        'read' => 'Lister',
        'create' => 'Créer',
        'edit' => 'Modifier',
        'delete' => 'Supprimer',
        'all' => 'Tout',
      ),
    ),
    'language' =>
    array (
      'name' => 'Nom',
      'iso' => 'Iso',
      'locale' => 'Locale',
      'defaultLanguage' => 'Langue par defaut',
      'formatDate' => 'Format de date',
      'formatDatetime' => 'Format de date et heure',
      'rtl' => 'Right to left',
    ),
    'translation' =>
    array (
      'domain' => 'Domaine',
      'key' => 'Texte source',
      'locale' => 'Langue',
      'value' => 'Traduction',
      'status' => 'Statut',
      'missing' => 'À traduire',
      'translated' => 'Traduite',
      'obsolete' => 'Obsolète',
    ),
    'menu' =>
    array (
      'name' => 'Nom',
      'code' => 'Code',
    ),
    'parameter' =>
    array (
      'mailFrom' => 'Adresse e-mail d\'expediteur',
      'mailFromName' => 'Nom e-mail expediteur',
      'ssl' => 'SSL',
      'gtm' => 'GTM',
      'tag' => 'Tag additionnel',
    ),
    'meta' =>
    array (
      'page' => 'Page',
      'app_index' => 'Accueil',
      'front_contact' => 'Contact',
      'metaTitle' => 'Meta title',
      'metaDescription' => 'Meta description',
      'metaKeywords' => 'Meta keywords',
      'rewrite' => 'Url simplifiée',
    ),
    'cmsCategory' =>
    array (
      'name' => 'Nom',
      'parent' => 'Catégorie parente',
      'metaTitle' => 'Meta title',
      'metaDescription' => 'Meta description',
      'metaKeywords' => 'Meta keywords',
      'rewrite' => 'Url simplifie',
      'active' => 'Actif',
    ),
    'cms' =>
    array (
      'addButtonCat' => 'Ajouter une catégorie',
      'name' => 'Nom',
      'category' => 'Catégorie',
      'content' => 'Contenu',
      'metaTitle' => 'Meta title',
      'metaDescription' => 'Meta description',
      'metaKeywords' => 'Meta keaywords',
      'rewrite' => 'Url simplifiée',
      'active' => 'Actif',
    ),
  ),
  'help' =>
  array (
    'role' =>
    array (
      'name' => 'Le nom peut changer librement : le code technique du rôle est fixé à sa création et ne bouge plus.',
      'superAdmin' => 'Tous les droits sur toutes les sections, quelle que soit la grille ci-dessous. Le dernier rôle à accès complet ne peut pas le perdre.',
      'authorization' => '« Toutes les sections » s\'applique à chaque section, « Tout » donne toutes les actions de la ligne. Sans effet pour un rôle à accès complet.',
    ),
    'menu' =>
    array (
      'code' => 'Identifiant technique utilisé par les templates, ex : dgtx_menu(\'main\'). Minuscules, chiffres, tirets.',
    ),
    'language' =>
    array (
      'iso' => 'norme iso, ex: "fr", "en"',
      'local' => 'norme iso locale, ex: "fr_FR", "en_EN"',
      'formatDate' => 'ex : d/m/Y',
      'formatDatetime' => 'ex : d/m/Y H:i:s',
    ),
  ),
);
