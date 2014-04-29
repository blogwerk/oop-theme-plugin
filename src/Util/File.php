<?php
/**
 * @package Blogwerk_Util
 * @author Michael Sebel <michael.sebel@blogwerk.com>
 * @copyright Blogwerk AG
 */

namespace Blogwerk\Util;

/**
 * Utility functions to work with files, extensions and directories
 *
 * @package Blogwerk_Util
 * @author Michael Sebel <michael.sebel@blogwerk.com>
 * @copyright Blogwerk AG
 */
class File
{

  /**
   * Extension eines Files zurückgeben inklusive . am Anfang
   * @param string $sFile zu bearbeitendes File
   * @return string Dateiendung mit Punkt
   */
  public static function getExtension($sFile)
  {
    return (substr($sFile, strripos($sFile, '.')));
  }

  /**
   * Gibt ein Array aller Files in einem Ordner zurück
   * @param string $sFolder Ordnername
   * @param bool $filesOnly nur filenamen, nicht ganze pfade
   * @return array Gefundene Files im Ordner
   */
  public static function getFiles($sFolder, $filesOnly = false)
  {
    $arrFiles = array();
    // Folder durchgehen
    if ($resDir = opendir($sFolder)) {
      while (($sFile = readdir($resDir)) !== false) {
        if (filetype($sFolder . $sFile) == 'file') {
          if ($filesOnly) {
            array_push($arrFiles, $sFile);
          } else {
            array_push($arrFiles, $sFolder . $sFile);
          }
        }
      }
    }
    // Ordner schliessen und Rückgabe
    closedir($resDir);
    return ($arrFiles);
  }

  /**
   * Gibt von einem Pfad nur den Ordnernamen zurück
   * @param string $sPath Pfad zur Datei
   * @return string the file folder
   */
  public static function getFileFolder($sPath)
  {
    return (substr($sPath, 0, strrpos($sPath, '/') + 1));
  }

  /**
   * Gibt von einem Pfad nur den Dateinamen zurück
   * @param string $sPath Pfad zur Datei
   * @return string the file only
   */
  public static function getFileOnly($sPath)
  {
    return (substr($sPath, strrpos($sPath, '/') + 1));
  }

  /**
   * Ordner rekursiv löschen (Egal ob Inhalt oder nicht)
   * @param string $sPath Pfad zu einem Ordner oder File
   * @return bool true/false ob Erfolgreich oder nicht
   */
  public static function deleteFolder($sPath)
  {
    // Prüfen ob der Ordner/das File existiert
    if (!file_exists($sPath)) return (false);
    // File löschen, wenn es ein File ist
    if (is_file($sPath)) return (unlink($sPath));
    // Durch den Ordner loopen
    $dir = dir($sPath);
    while (false !== $entry = $dir->read()) {
      // Pointer überspringen
      if ($entry == '.' || $entry == '..') continue;
      // Rekursiv wieder aufrufen für Subfolder
      self::deleteFolder("$sPath/$entry");
    }
    // Resourcen schliessen
    $dir->close();
    return (rmdir($sPath));
  }

  /**
   * Gibt Timestamp einer Datei zurück (Änderungsdatum
   * @param $file File, welches geprüft wird
   * @return int Timestamp des Änderungsdatum
   */
  public static function getStamp($file)
  {
    if (file_exists($file)) {
      return (filemtime($file));
    } else {
      return (0);
    }
  }
}