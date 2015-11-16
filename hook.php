<?php
/*
 *
 -------------------------------------------------------------------------
 Plugin GLPI News
 Copyright (C) 2015 by teclib.
 http://www.teclib.com
 -------------------------------------------------------------------------
 LICENSE
 This file is part of Plugin GLPI News.
 Plugin GLPI News is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.
 Plugin GLPI News is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.
 You should have received a copy of the GNU General Public License
 along with Plugin GLPI News. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
*/

require_once "inc/alert.class.php";
require_once "inc/profile.class.php";

function plugin_news_install() {
   global $DB;

   $plugin = new Plugin();

   $found = $plugin->find("name = 'news'");

   $pluginNews = array_shift($found);

   $migration = new Migration($pluginNews['version']);

   /* New install */
   if (! TableExists('glpi_plugin_news_alerts')) {
      $DB->query("
         CREATE TABLE IF NOT EXISTS `glpi_plugin_news_alerts` (
         `id` INT NOT NULL AUTO_INCREMENT,
         `date_mod` DATETIME NOT NULL,
         `name` VARCHAR(255) NOT NULL,
         `message` TEXT NOT NULL,
         `date_start` DATE NOT NULL,
         `date_end` DATE NOT NULL,
         `is_deleted` TINYINT(1) NOT NULL,
         `profiles_id` INT NOT NULL,
         `entities_id` INT NOT NULL,
         `is_recursive` TINYINT(1) NOT NULL,
         PRIMARY KEY (`id`)
         ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
      ");
   }

   /* Remove old table */
   if (TableExists('glpi_plugin_news_profiles')) {
      $DB->query("DROP TABLE IF EXISTS `glpi_plugin_news_profiles`;");
   }

   PluginNewsProfile::createFirstAccess($_SESSION['glpiactiveprofile']['id']);

   return true;
}

function plugin_news_uninstall() {
   global $DB;

   $DB->query("DROP TABLE IF EXISTS `glpi_plugin_news_alerts`;");
   $DB->query("DROP TABLE IF EXISTS `glpi_plugin_news_profiles`;");

   $DB->query("DELETE FROM `glpi_profiles` WHERE `name` LIKE '%plugin_news%';");

   return true;
}
