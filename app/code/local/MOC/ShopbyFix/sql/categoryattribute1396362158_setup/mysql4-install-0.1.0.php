<?php
$installer = $this;
$installer->startSetup();

$installer->addAttribute("catalog_category", "description_template",  array(
    "group"    => "SEO Templates",
    "type"     => "text",
    "backend"  => "",
    "frontend" => "",
    "label"    => "Description - template",
    "input"    => "textarea",
    "class"    => "",
    "source"   => "",
    "global"   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    "visible"  => true,
    "required" => false,
    "user_defined"  => false,
    "default" => "",
    "searchable" => false,
    "filterable" => false,
    "comparable" => false,
    "wysiwyg_enabled"   => 1,
    "is_html_allowed_on_front" => true,
    "visible_on_front"  => false,
    "unique"     => false,
    "note"       => ""

    ));


$installer->addAttribute("catalog_category", "meta_title_template",  array(
    "group"    => "SEO Templates",
    "type"     => "text",
    "backend"  => "",
    "frontend" => "",
    "label"    => "Titre de la page - template",
    "input"    => "textarea",
    "class"    => "",
    "source"   => "",
    "global"   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    "visible"  => true,
    "required" => false,
    "user_defined"  => false,
    "default" => "",
    "searchable" => false,
    "filterable" => false,
    "comparable" => false,
    "visible_on_front"  => false,
    "unique"     => false,
    "note"       => ""

    ));

$installer->addAttribute("catalog_category", "meta_description_template",  array(
    "group"    => "SEO Templates",
    "type"     => "text",
    "backend"  => "",
    "frontend" => "",
    "label"    => "Méta description - template",
    "input"    => "textarea",
    "class"    => "",
    "source"   => "",
    "global"   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    "visible"  => true,
    "required" => false,
    "user_defined"  => false,
    "default" => "",
    "searchable" => false,
    "filterable" => false,
    "comparable" => false,
    "visible_on_front"  => false,
    "unique"     => false,
    "note"       => ""

    ));


$installer->endSetup();
     