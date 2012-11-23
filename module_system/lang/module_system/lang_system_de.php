<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2012 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$					    *
********************************************************************************************************/
//Edited with Kajona Language Editor GUI, see www.kajona.de and www.mulchprod.de for more information
//Kajona Language Editor Core Build 385

//editable entries
$lang["_admin_nr_of_rows_"]              = "Anzahl Datensätze pro Seite:";
$lang["_admin_nr_of_rows_hint"]          = "Anzahl an Datensätzen in den Admin-Listen, sofern das Modul dies unterstützt. Kann von einem Modul überschrieben werden!";
$lang["_admin_only_https_"]              = "Admin nur per HTTPS:";
$lang["_admin_only_https_hint"]          = "Bevorzugt die Verwendung von HTTPS im Adminbereich. Der Webserver muss hierfür HTTPS unterstützen.";
$lang["_remoteloader_max_cachetime_"]    = "Cachedauer externer Quellen:";
$lang["_remoteloader_max_cachetime_hint"] = "Cachedauer in Sekunden für extern nachgeladene Inhalte (z.B. RSS-Feeds).";
$lang["_system_admin_email_"]            = "Admin E-Mail:";
$lang["_system_admin_email_hint"]        = "Falls ausgefüllt, wird im Fall eines schweren Fehlers eine E-Mail an diese Adresse gesendet.";
$lang["_system_browser_cachebuster_"]    = "Browser-Cachebuster";
$lang["_system_browser_cachebuster_hint"] = "Dieser Wert wird als GET-Parameter allen Verweisen auf JS/CSS-Dateien angehängt. Durch hochzählen des Wertes kann der Browser dazu gezwungen werden die entsprechenden Dateien erneut vom Server herunter zu laden, unabhängig von den Caching-Einstellungen des Browsers und den vom Server gesendeten HTTP-Headern. Der Wert wird über den Systemtask 'Cache leeren' automatisch hochgezählt.";
$lang["_system_changehistory_enabled_"]  = "Änderungshistorie aktiv:";
$lang["_system_dbdump_amount_"]          = "Anzahl DB-Dumps:";
$lang["_system_dbdump_amount_hint"]      = "Definiert, wie viele Datenbank-Sicherungen vorgehalten werden sollen.";
$lang["_system_graph_type_"]             = "Verwendete Chart-Bibliothek: ";
$lang["_system_graph_type_hint"]         = "Gültige Werte: pchart, ezc, flot. pChart muss gesondern heruntergeladen und installiert werden, ezc benötigt im Optimalfall das PHP-Modul 'cairo'.<br />Siehe hierzu auch <a href=\"http://www.kajona.de/nicecharts.html\" taget=\"_blank\">http://www.kajona.de/nicecharts.html</a>";
$lang["_system_lock_maxtime_"]           = "Maximale Sperrdauer:";
$lang["_system_lock_maxtime_hint"]       = "Nach der angegebenen Dauer in Sekunden werden gesperrte Datensätze automatisch wieder freigegeben.";
$lang["_system_mod_rewrite_"]            = "URL-Rewriting:";
$lang["_system_mod_rewrite_hint"]        = "Schaltet URL-Rewriting für Nice-URLs ein oder aus. Das Apache-Modul \"mod_rewrite\" muss dazu installiert sein und in der .htaccess-Datei aktiviert werden!";
$lang["_system_portal_disable_"]         = "Portal deaktiviert:";
$lang["_system_portal_disable_hint"]     = "Diese Einstellung aktiviert/deaktiviert das gesamte Portal.";
$lang["_system_portal_disablepage_"]     = "Zwischenseite:";
$lang["_system_portal_disablepage_hint"] = "Diese Seite wird angezeigt, wenn das Portal deaktiviert wurde.";
$lang["_system_release_time_"]           = "Dauer einer Session:";
$lang["_system_release_time_hint"]       = "Nach dieser Dauer in Sekunden wird eine Session automatisch ungültig.";
$lang["_system_use_dbcache_"]            = "Datenbankcache aktiv:";
$lang["_system_use_dbcache_hint"]        = "Aktiviert/deaktiviert den systeminternen Cache für Datenbankabfragen.";
$lang["about_part1"]                     = "<h2>Kajona V4 - Open Source Content Management System</h2>Kajona V 3.4.9 (4.0 Beta), Codename \"revolution\"<br /><br /><a href=\"http://www.kajona.de\" target=\"_blank\">www.kajona.de</a><br /><a href=\"mailto:info@kajona.de\" target=\"_blank\">info@kajona.de</a><br /><br />Für weitere Infomationen, Support oder bei Anregungen besuchen Sie einfach unsere Webseite.<br />Support erhalten Sie auch in unserem <a href=\"http://board.kajona.de/\" target=\"_blank\">Forum</a>.";
$lang["about_part2_header"]              = "<h2>Entwicklungsleitung</h2>";
$lang["about_part2a_header"]             = "<h2>Contributors / Entwickler</h2>";
$lang["about_part2b_header"]             = "<h2>Übersetzungen</h2>";
$lang["about_part4"]                     = "<h2>Spenden</h2><p>Wenn Ihnen Kajona gefällt und Sie das Projekt unterstützen möchten können Sie hier an das Projekt spenden: </p> <form method=\"post\" action=\"https://www.paypal.com/cgi-bin/webscr\" target=\"_blank\"><input type=\"hidden\" value=\"_donations\" name=\"cmd\" /> <input type=\"hidden\" value=\"donate@kajona.de\" name=\"business\" /> <input type=\"hidden\" value=\"Kajona Development\" name=\"item_name\" /> <input type=\"hidden\" value=\"0\" name=\"no_shipping\" /> <input type=\"hidden\" value=\"1\" name=\"no_note\" /> <input type=\"hidden\" value=\"EUR\" name=\"currency_code\" /> <input type=\"hidden\" value=\"0\" name=\"tax\" /> <input type=\"hidden\" value=\"PP-DonationsBF\" name=\"bn\" /> <input type=\"submit\" name=\"submit\" value=\"Spenden via PayPal\" class=\"inputSubmit\" /></form>";
$lang["actionAbout"]                     = "Über Kajona";
$lang["actionAspects"]                   = "Aspekte";
$lang["actionChangelog"]                 = "Änderungshistorie";
$lang["actionList"]                      = "Installierte Module";
$lang["actionSystemInfo"]                = "Systeminformationen";
$lang["actionSystemSessions"]            = "Sessions";
$lang["actionSystemSettings"]            = "Systemeinstellungen";
$lang["actionSystemTasks"]               = "System-Tasks";
$lang["actionSystemlog"]                 = "System-Log";
$lang["anzahltabellen"]                  = "Anzahl Tabellen";
$lang["aspect_create"]                   = "Neuer Aspekt";
$lang["aspect_delete_question"]          = "Möchten Sie den Aspekt &quot;<b>%%element_name%%</b>&quot; wirklich löschen?";
$lang["aspect_edit"]                     = "Aspekt bearbeiten";
$lang["aspect_isDefault"]                = "Standard Aspekt";
$lang["aspect_list_empty"]               = "Keine Aspekete angelegt";
$lang["cache_entry_size"]                = "Größe";
$lang["cache_hash1"]                     = "Hash 1";
$lang["cache_hash2"]                     = "Hash 2";
$lang["cache_leasetime"]                 = "Gültig bis";
$lang["cache_source"]                    = "Quelle";
$lang["change_action"]                   = "Aktion";
$lang["change_module"]                   = "Modul";
$lang["change_newvalue"]                 = "Neuer Wert";
$lang["change_oldvalue"]                 = "Alter Wert";
$lang["change_property"]                 = "Eigenschaft";
$lang["change_record"]                   = "Objekt";
$lang["change_type_setting"]             = "Einstellung";
$lang["change_user"]                     = "Benutzer";
$lang["dateStyleLong"]                   = "d.m.Y H:i:s";
$lang["dateStyleShort"]                  = "d.m.Y";
$lang["datenbankclient"]                 = "Datenbankclient";
$lang["datenbankserver"]                 = "Datenbankserver";
$lang["datenbanktreiber"]                = "Datenbanktreiber";
$lang["datenbankverbindung"]             = "Datenbankverbindung";
$lang["db"]                              = "Datenbank";
$lang["desc"]                            = "Rechte ändern an:";
$lang["dialog_cancelButton"]             = "abbrechen";
$lang["dialog_deleteButton"]             = "Ja, löschen";
$lang["dialog_deleteHeader"]             = "Löschen bestätigen";
$lang["dialog_loadingHeader"]            = "Bitte warten";
$lang["diskspace_free"]                  = " (frei/gesamt)";
$lang["errorintro"]                      = "Bitte alle benötigten Felder ausfüllen!";
$lang["errorlevel"]                      = "Error Level";
$lang["executiontimeout"]                = "Execution Timeout";
$lang["fehler_setzen"]                   = "Fehler beim Speichern der Rechte";
$lang["filebrowser"]                     = "Datei auswählen";
$lang["form_aspect_default"]             = "Standard-Aspekt:";
$lang["form_aspect_name"]                = "Name:";
$lang["form_aspect_name_hint"]           = "Der Name wird als interner Titel verwendet. Um einen Aspekt zu lokalisieren, kann dieser über einen Lang-Eintrag (aspect_NAME) gelabelt werden.";
$lang["gd"]                              = "GD-Lib";
$lang["geladeneerweiterungen"]           = "Geladene Erweiterungen";
$lang["gifread"]                         = "GIF Read Support";
$lang["gifwrite"]                        = "GIF Write Support";
$lang["groessedaten"]                    = "Größe Daten";
$lang["groessegesamt"]                   = "Größe Gesamt";
$lang["inputtimeout"]                    = "Input Timeout";
$lang["installer_config_dbdriver"]       = "Datenbanktreiber:";
$lang["installer_config_dbhostname"]     = "Datenbankserver:";
$lang["installer_config_dbname"]         = "Datenbankname:";
$lang["installer_config_dbpassword"]     = "Datenbankpasswort:";
$lang["installer_config_dbport"]         = "Datenbankport:";
$lang["installer_config_dbportinfo"]     = "Für den Standardport bitte leer lassen";
$lang["installer_config_dbprefix"]       = "Tabellenpräfix:";
$lang["installer_config_dbusername"]     = "Datenbankbenutzer:";
$lang["installer_config_intro"]          = "<b>Datenbankeinstellungen erfassen</b><br /><br />Anmerkung: Der Webserver benötigt Schreibrechte auf die Datei /system/config/config.php.<br />Für den Fall, dass Sie einen dieser Werte leer lassen möchten, bearbeiten Sie bitte die Datei /system/config/config.php manuell mit einem Texteditor, Näheres hierzu finden Sie im Handbuch.<br />";
$lang["installer_config_write"]          = "In config.php speichern";
$lang["installer_dbdriver_na"]           = "Es tut uns leid, aber der gewählte Datenbanktreiber ist auf dem System nicht verfügbar. Bitte installieren Sie die nachstehende PHP-Erweiterung um den Treiber zu verwenden:";
$lang["installer_dbdriver_oci8"]         = "Achtung: Der Oracle-Treiber befindet sich noch im Teststadium.";
$lang["installer_dbdriver_sqlite3"]      = "Der SQLite-Treiber legt die Datenbank im Verzeichnis /project/dbdumps ab. Hierbei gilt der Datenbankname als Dateiname, alle anderen Werte sind nicht weiter von Belang.";
$lang["installer_elements_found"]        = "<b>Installation der Seitenelemente</b><br /><br />Bitte wählen Sie die Seitenelemente aus, die Sie installieren möchten:<br /><br />";
$lang["installer_finish_closer"]         = "<br />Wir wünschen viel Spaß mit Kajona!";
$lang["installer_finish_hints"]          = "Sie sollten nun die Schreibrechte auf die Datei /project/system/config/config.php auf Leserechte zurücksetzen.<br />Zusätzlich sollte aus Sicherheitsgründen die Datei /installer.php unbedingt komplett gelöscht werden.<br /><br /><br />Die Administrationsoberfläche erreichen Sie nun unter:<br />&nbsp;&nbsp;&nbsp;&nbsp;<a href=\""._webpath_."/admin\">"._webpath_."/admin</a><br /><br />Das Portal erreichen Sie unter:<br />&nbsp;&nbsp;&nbsp;&nbsp;<a href=\""._webpath_."/\">"._webpath_."</a><br /><br />";
$lang["installer_finish_hints_update"]   = "<b>Achtung: Wenn Sie eine Update eines v3 Systems durchführen, dann sollten Sie nach dem Upgrade aller Module die Post-Update Scripte ausführen. </b><br /><a href=\"_webpath_/v3_v4_postupdate.php\">Post-Update ausführen</a><br /><br />";
$lang["installer_finish_intro"]          = "<b>Installation abgeschlossen</b><br /><br />";
$lang["installer_dbcx_error"]            = "Verbindung zur Datenbank konnte nicht aufgebaut werden. Bitte prüfen Sie die angegebenen Zugangsdaten.";
$lang["installer_given"]                 = "vorhanden";
$lang["installer_install"]               = "Installieren";
$lang["installer_installpe"]             = "Seitenelemente installieren";
$lang["installer_loaded"]                = "geladen";
$lang["installer_login_email"]           = "E-Mail:";
$lang["installer_login_installed"]       = "<br />Das System wurde bereits mit einem Admin-Benutzer installiert.<br />";
$lang["installer_login_intro"]           = "<b>Admin-Benutzer einrichten</b><br /><br />Bitte geben Sie hier einen Benutzernamen und ein Passwort an.<br />Diese Daten werden später als Zugang zur Administration verwendet.<br />Aus Sicherheitsgründen sollten Sie Benutzernamen wie \"admin\" oder \"administrator\" vermeiden.<br /><br />";
$lang["installer_login_password"]        = "Passwort:";
$lang["installer_login_save"]            = "Benutzer anlegen";
$lang["installer_login_username"]        = "Benutzername:";
$lang["installer_missing"]               = "fehlen";
$lang["installer_mode_auto"]             = "Automatische Installation";
$lang["installer_mode_auto_hint"]        = "Alle verfügbaren Module und Beispielinhalte werden installiert.";
$lang["installer_mode_hint"]             = "Achtung: Beim Update eines System sollte der manuelle Modus gewählt werden.";
$lang["installer_mode_manual"]           = "Manuelle Installation";
$lang["installer_mode_manual_hint"]      = "Die zu installierende Module können manuell ausgewählt werden, die Installation der Beispielinhalte kann überprungen werden.";
$lang["installer_module_notinstalled"]   = "Modul ist nicht installiert";
$lang["installer_modules_found"]         = "<b>Installation/Update der Module</b><br /><br />Bitte wählen Sie die Module aus, die Sie installieren möchten:<br /><br />";
$lang["installer_modules_needed"]        = "Zur Installation benötigte Module: ";
$lang["installer_next"]                  = "Nächster Schritt >";
$lang["installer_nloaded"]               = "fehlt";
$lang["installer_phpcheck_folder"]       = "Schreibrechte auf ";
$lang["installer_phpcheck_intro"]        = "<b>Herzlich Willkommen</b><br /><br />";
$lang["installer_phpcheck_intro2"]       = "<br />Die Installation des Systems erfolgt in mehreren Schritten: <br />Rechtepüfung, DB-Konfiguration, Zugangsdaten zur Administration, Modulinstallation, Elementinstallation und Installation der Beispielinhalte.<br /><br />Je nach Modulauswahl kann die Anzahl dieser Schritte abweichen.<br /><br /> <b>Vor einem Systemupdate lesen Sie bitte die<br /><a href=\"http://www.kajona.de/update_311_to_320.html\" target=\"_blank\">Updatehinweise von 3.1.x auf 3.2.0</a><br /><a href=\"http://www.kajona.de/update_32x_to_330.html\" target=\"_blank\">Updatehinweise von 3.2.x auf 3.3.0</a><br /><a href=\"http://www.kajona.de/update_33x_to_340.html\" target=\"_blank\">Updatehinweise von 3.3.x auf 3.4.0</a>.</b><br /><br /><br />Es werden die Schreibrechte einzelner Dateien und Verzeichnisse sowie<br />die Verfügbarkeit benötigter PHP-Module überprüft:<br />";
$lang["installer_phpcheck_lang"]         = "Um den Installer in einer anderen Sprache zu laden, bitte einen der folgenden Links verwenden:<br /><br />";
$lang["installer_phpcheck_module"]       = "PHP-Modul ";
$lang["installer_prev"]                  = "< Vorheriger Schritt";
$lang["installer_samplecontent"]         = "<b>Installation der Beispielinhalte</b><br /><br />Das Modul Samplecontent erstellt einige Standard-Seiten und Navigationen.<br />Je nach installierten Modulen werden verschiedene Beispielinhalte installiert.<br /><br /><br />";
$lang["installer_step_adminsettings"]    = "Administrationszugang";
$lang["installer_step_dbsettings"]       = "Datenbankeinstellungen";
$lang["installer_step_finish"]           = "Abschluss";
$lang["installer_step_modeselect"]       = "Installationsart wählen";
$lang["installer_step_modules"]          = "Module";
$lang["installer_step_phpsettings"]      = "PHP-Konfiguration";
$lang["installer_step_samplecontent"]    = "Beispielinhalte";
$lang["installer_systemlog"]             = "System Log";
$lang["installer_systemversion_needed"]  = "Minimal benötigte Version: ";
$lang["installer_update"]                = "Update auf ";
$lang["installer_versioninstalled"]      = "Installierte Version: ";
$lang["jpg"]                             = "JPG Support";
$lang["keinegd"]                         = "Keine GD-Lib installiert";
$lang["log_empty"]                       = "Keine Einträge im System-Logfile vorhanden";
$lang["login_xml_error"]                 = "Login fehlgeschlagen";
$lang["login_xml_succeess"]              = "Login erfolgreich";
$lang["logout_xml"]                      = "Logout erfolgreich";
$lang["mail_body"]                       = "Inhalt:";
$lang["mail_cc"]                         = "Empfänger in Kopie:";
$lang["mail_recipient"]                  = "Empfänger:";
$lang["mail_send_error"]                 = "Fehler beim Versenden der E-Mail. Bitte versuchen Sie die letzte Aktion erneut.";
$lang["mail_send_success"]               = "E-Mail erfolgreich verschickt.";
$lang["mail_subject"]                    = "Betreff:";
$lang["memorylimit"]                     = "Memory Limit";
$lang["messageprovider_exceptions_name"] = "System-Fehlermeldungen";
$lang["modul_aspectedit"]                = "Aspekte bearbeiten";
$lang["modul_rechte_root"]               = "Root-Rechte";
$lang["modul_sortdown"]                  = "Nach unten verschieben";
$lang["modul_sortup"]                    = "Nach oben verschieben";
$lang["modul_status_disabled"]           = "Modul aktiv schalten (ist inaktiv)";
$lang["modul_status_enabled"]            = "Modul inaktiv schalten (ist aktiv)";
$lang["modul_status_system"]             = "Hmmm. Den System-Kernel deaktivieren? Zuvor bitte format c: ausführen!";
$lang["modul_titel"]                     = "System";
$lang["moduleRightsTitle"]               = "Rechte";
$lang["numberStyleDecimal"]              = ",";
$lang["numberStyleThousands"]            = ".";
$lang["operatingsystem"]                 = "Betriebssystem";
$lang["pageview_forward"]                = "Weiter";
$lang["pageview_total"]                  = "Gesamt: ";
$lang["php"]                             = "PHP";
$lang["png"]                             = "PNG Support";
$lang["postmaxsize"]                     = "Post Max Size";
$lang["quickhelp_change"]                = "Mit Hilfe dieses Formulares können die Rechte eines Datensatzes angepasst werden.<br />Je nach dem, welchem Modul der Datensatz zugeordnet wurde, kann die Anzahl der möglichen zu konfigurierenden Rechte variieren.";
$lang["quickhelp_list"]                  = "Die Liste der Module gibt eine schnelle Übersicht über die aktuell im System installierten Module.<br />Zusätzlich werden die aktuell installierten Versionen der installierten Module genannt, ebenso das ursprüngliche Installationsdatum des Moduls.<br />Über die Rechte des Moduls kann der Modul-Rechte-Knoten bearbeitet werden, von welchem die Inhalte bei aktivierter Rechtevererbung ihre Einstellungen erben.<br />Durch Verschieben der Module in der Liste lässt sich die Reihenfolge in der Modulnavigation anpassen.";
$lang["quickhelp_moduleList"]            = "Die Liste der Module gibt eine schnelle Übersicht über die aktuell im System installierten Module.<br />Zusätzlich werden die aktuell installierten Versionen der installierten Module genannt, ebenso das ursprüngliche Installationsdatum des Moduls.<br />Über den Rechte-Button der Module können die jeweiligen Modul-Root-Rechte bearbeitet werden, welche an einzelne Datensätze des Moduls vererbt werden (solange die Rechteerbung des Datensatzes aktiviert ist).<br />Durch Verschieben der Module in der Liste lässt sich die Reihenfolge in der Modulnavigation anpassen.";
$lang["quickhelp_systemInfo"]            = "Kajona versucht an dieser Stelle, ein paar Informationen über das System heraus zu finden, auf welchem sich die Installation befindet.";
$lang["quickhelp_systemSettings"]        = "Hier können grundlegende Einstellungen des Systems vorgenommen werden. Hierfür kann jedes Modul beliebige Einstellungsmöglichkeiten anbieten. Die hier vorgenommenen Einstellungen sollten mit Vorsicht verändert werden, falsche Einstellungen können das System im schlimmsten Fall unbrauchbar machen.<br /><br />Hinweis: Werden Werte an einem Modul geändert, so muss für JEDES Modul der Speichern-Button gedrückt werden. Ein Abändern der Einstellungen verschiedener Module wird beim Speichern nicht übernommen. Es werden nur die Werte der zum Speichern-Button zugehörigen Felder übernommen.";
$lang["quickhelp_systemTasks"]           = "Systemtasks sind kleine Programme, die alltägliche Aufaben wie Wartungsarbeiten im System übernehmen.<br />Hierzu gehört das Sichern der Datenbank und ggf. das Rückspielen einer Sicherung in das System.";
$lang["quickhelp_systemlog"]             = "Das Systemlogbuch gibt die Einträge des Logfiles aus, in welche die Module Nachrichten schreiben können.<br />Die Feinheit des Loggings kann in der config-Datei (/system/config/config.php) eingestellt werden.";
$lang["quickhelp_title"]                 = "Schnellhilfe";
$lang["quickhelp_updateCheck"]           = "Mit der Aktion Updatecheck werden die Versionsnummern der im System installierten Module mit den Versionsnummern der aktuell verfügbaren Module verglichen. Sollte ein Modul nicht mehr in der neusten Verion installiert sein, so gibt Kajona in der Zeile dieses Moduls einen Hinweis hierzu aus.";
$lang["send"]                            = "Versenden";
$lang["server"]                          = "Webserver";
$lang["session_activity"]                = "Aktivität";
$lang["session_admin"]                   = "Administration, Modul: ";
$lang["session_loggedin"]                = "angemeldet";
$lang["session_loggedout"]               = "Gast";
$lang["session_logout"]                  = "Session beenden";
$lang["session_portal"]                  = "Portal, Seite: ";
$lang["session_portal_imagegeneration"]  = "Bilderzeugung";
$lang["session_status"]                  = "Status";
$lang["session_username"]                = "Benutzer";
$lang["session_valid"]                   = "Gültig bis";
$lang["setAbsolutePosOk"]                = "Speichern der Position erfolgreich";
$lang["setPrevIdOk"]                     = "Speichern des neuen Eltern-Knotens erfolgreich";
$lang["setStatusError"]                  = "Fehler beim Ändern des Status";
$lang["setStatusOk"]                     = "Ändern des Status erfolgreich";
$lang["settings_updated"]                = "Einstellungen wurden geändert.";
$lang["setzen_erfolg"]                   = "Rechte erfolgreich gespeichert";
$lang["speicherplatz"]                   = "Speicherplatz";
$lang["status_active"]                   = "Status ändern (ist aktiv)";
$lang["status_inactive"]                 = "Status ändern (ist inaktiv)";
$lang["system_cache"]                    = "Cache";
$lang["systeminfo_php_regglobal"]        = "Register globals";
$lang["systeminfo_php_safemode"]         = "Safe mode";
$lang["systeminfo_php_urlfopen"]         = "Allow url fopen";
$lang["systeminfo_webserver_modules"]    = "Geladene Module";
$lang["systeminfo_webserver_version"]    = "Webserver";
$lang["systemtask_cacheSource_source"]   = "Cache-Arten:";
$lang["systemtask_cancel_execution"]     = "Ausführung beenden";
$lang["systemtask_close_dialog"]         = "OK";
$lang["systemtask_compresspicuploads_done"] = "Die Bildverkleinerung und -komprimierung wurde abgeschlossen.";
$lang["systemtask_compresspicuploads_found"] = "Gefundene Bilder";
$lang["systemtask_compresspicuploads_height"] = "Maximale Höhe (Pixel)";
$lang["systemtask_compresspicuploads_hint"] = "Um Speicherplatz zu sparen können Sie alle hochgeladenen Bilder im Ordner \"/portal/pics/upload\" auf die angegebene Maximalgröße verkleinern und neu komprimieren lassen.<br />Beachten Sie, dass dieser Vorgang nicht rückgängig gemacht werden kann und es ggf. zu Qualitätseinbußen kommen kann.<br />Der Vorgang kann einige Minuten in Anspruch nehmen.";
$lang["systemtask_compresspicuploads_name"] = "Hochgeladene Bilder komprimieren";
$lang["systemtask_compresspicuploads_processed"] = "Bearbeitete Bilder";
$lang["systemtask_compresspicuploads_width"] = "Maximale Breite (Pixel)";
$lang["systemtask_dbconsistency_curprev_error"] = "Folgende Eltern-Kind Beziehungen sind fehlerhaft (fehlender Elternteil):";
$lang["systemtask_dbconsistency_curprev_ok"] = "Alle Eltern-Kind Beziehungen sind korrekt";
$lang["systemtask_dbconsistency_date_error"] = "Folgende Datum-Records sind fehlerhaft (fehlender System-Record):";
$lang["systemtask_dbconsistency_date_ok"] = "Alle Datum-Records haben einen zugehörigen System-Record";
$lang["systemtask_dbconsistency_firstlevel_error"] = "Nicht alle Knoten auf erster Ebene gehören zu einem Modul";
$lang["systemtask_dbconsistency_firstlevel_ok"] = "Alle Knoten auf erster Ebene gehören zu einem Modul";
$lang["systemtask_dbconsistency_name"]   = "Datenbankkonsistenz überprüfen";
$lang["systemtask_dbconsistency_right_error"] = "Folgende Rechte-Records sind fehlerhaft (fehlender System-Record):";
$lang["systemtask_dbconsistency_right_ok"] = "Alle Rechte-Records haben einen zugehörigen System-Record";
$lang["systemtask_dbexport_error"]       = "Fehler beim Sichern der Datenbank. Weitere Informationen finden Sie im Logfile.";
$lang["systemtask_dbexport_exclude_intro"] = "Wenn aktiviert werden aus der Sicherung sowohl die Tabelle der Statistiken als auch die Tabelle des Caches ausgenommen.";
$lang["systemtask_dbexport_excludetitle"] = "Tabellen ausschließen:";
$lang["systemtask_dbexport_name"]        = "Datenbank sichern";
$lang["systemtask_dbexport_success"]     = "Sicherung erfolgreich angelegt";
$lang["systemtask_dbimport_datefileinfo"] = "Zeitstempel gemäß Dateiinfo:";
$lang["systemtask_dbimport_datefilename"] = "Zeitstempel gemäß Dateiname:";
$lang["systemtask_dbimport_error"]       = "Fehler beim Einspielen der Sicherung";
$lang["systemtask_dbimport_file"]        = "Vorhandene Sicherungen:";
$lang["systemtask_dbimport_name"]        = "Datenbank importieren";
$lang["systemtask_dbimport_success"]     = "Sicherung erfolgreich eingespielt";
$lang["systemtask_dialog_title"]         = "Systemtask wird ausgeführt";
$lang["systemtask_dialog_title_done"]    = "Systemtask abgeschlossen";
$lang["systemtask_filedump_error"]       = "Während der Sicherung ist ien Fehler aufgetreten.";
$lang["systemtask_filedump_name"]        = "Sicherung des Dateisystems erstellen";
$lang["systemtask_filedump_success"]     = "Die Sicherung wurde erfolgreich angelegt. <br/>Aus Sicherheitsgründen sollte die Sicherung schnellstmöglich vom Server entfernt werden. <br />Name der Sicherungsdatei:&nbsp;";
$lang["systemtask_flushcache_all"]       = "Alle Einträge";
$lang["systemtask_flushcache_error"]     = "Ein Fehler ist aufgetreten.";
$lang["systemtask_flushcache_name"]      = "Globalen Cache leeren";
$lang["systemtask_flushcache_success"]   = "Der Cache wurde geleert.";
$lang["systemtask_flushpiccache_deleted"] = "<br />Anzahl gelöschter Bilder: ";
$lang["systemtask_flushpiccache_done"]   = "Leeren abgeschlossen.";
$lang["systemtask_flushpiccache_name"]   = "Bildercache leeren";
$lang["systemtask_flushpiccache_skipped"] = "<br />Anzahl übersprungener Bilder: ";
$lang["systemtask_group_cache"]          = "Cache";
$lang["systemtask_group_database"]       = "Datenbank";
$lang["systemtask_group_default"]        = "Verschiedenes";
$lang["systemtask_group_ldap"]           = "Ldap";
$lang["systemtask_group_pages"]          = "Seiten";
$lang["systemtask_group_stats"]          = "Statistiken";
$lang["systemtask_progress"]             = "Fortschritt:";
$lang["systemtask_run"]                  = "Ausführen";
$lang["systemtask_runningtask"]          = "Task:";
$lang["systemtask_status_error"]         = "Fehler beim Setzen des Status.";
$lang["systemtask_status_success"]       = "Der Status wurde erfolgreich gesetzt.";
$lang["systemtask_systemstatus_active"]  = "Aktiv";
$lang["systemtask_systemstatus_inactive"] = "Inaktiv";
$lang["systemtask_systemstatus_name"]    = "Status eines Datensatzes setzen";
$lang["systemtask_systemstatus_status"]  = "Status:";
$lang["systemtask_systemstatus_systemid"] = "Systemid:";
$lang["titel_erben"]                     = "Rechte erben:";
$lang["titel_leer"]                      = "<em>Kein Titel hinterlegt</em>";
$lang["titel_root"]                      = "Rechte-Root-Satz";
$lang["titleTime"]                       = "Uhr";
$lang["toolsetCalendarMonth"]            = "\"Januar\", \"Februar\", \"M\\u00E4rz\", \"April\", \"Mai\", \"Juni\", \"Juli\", \"August\", \"September\", \"Oktober\", \"November\", \"Dezember\"";
$lang["toolsetCalendarWeekday"]          = "\"So\", \"Mo\", \"Di\", \"Mi\", \"Do\", \"Fr\", \"Sa\"";
$lang["update_available"]                = "Bitte updaten!";
$lang["update_invalidXML"]               = "Die Antwort vom Server war leider nicht korrekt. Bitte versuchen Sie die letzte Aktion erneut.";
$lang["update_module_localversion"]      = "Diese Installation";
$lang["update_module_name"]              = "Modul";
$lang["update_module_remoteversion"]     = "Verfügbar";
$lang["update_nodom"]                    = "Diese PHP-Installation unterstützt kein XML-DOM. Dies ist für den Update-Check erforderlich.";
$lang["update_nofilefound"]              = "Die Liste der Updates konnte nicht geladen werden.<br />Gründe hierfür können sein, dass auf diesem System der PHP-Config-Wert 'allow_url_fopen' auf 'off' gesetzt wurde, oder das System keine Unterstützung für Sockets bietet.";
$lang["update_nourlfopen"]               = "Für diese Funktion muss der Wert &apos;allow_url_fopen&apos; in der PHP-Konfiguration auf &apos;on&apos; gesetzt sein!";
$lang["uploadmaxsize"]                   = "Upload Max Size";
$lang["uploads"]                         = "Uploads";
$lang["version"]                         = "Version";
$lang["warnung_settings"]                = "!! ACHTUNG !!<br />Bei folgenden Einstellungen können falsche Werte das System unbrauchbar machen!";

//non-editable entries
$lang["permissions_default_header"]      = array(0 => "Anzeigen", 1 => "Bearbeiten", 2 => "Löschen", 3 => "Rechte", 4 => "", 5 => "", 6 => "", 7 => "", 8 => "");
$lang["permissions_header"]              = array(0 => "Anzeigen", 1 => "Bearbeiten", 2 => "Löschen", 3 => "Rechte", 4 => "Einstellungen",  5 => "Systemtasks", 6 => "Systemlog", 7 => "", 8 => "Aspekte");
$lang["permissions_root_header"]         = array(0 => "Anzeigen", 1 => "Bearbeiten", 2 => "Löschen", 3 => "Rechte", 4 => "Universal 1", 5 => "Universal 2", 6 => "Universal 3", 7 => "Universal 4", 8 => "Universal 5" );
