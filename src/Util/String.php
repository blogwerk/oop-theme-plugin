<?php
/**
 * Utility functions to work with strings and regex
 *
 * @package Blogwerk_Util
 * @author Michael Sebel <michael.sebel@blogwerk.com>
 * @copyright Blogwerk AG
 */

namespace Blogwerk\Util;

use \DOMDocument;
use \DOMXpath;

/**
 * Utility functions to work with strings and regex
 *
 * @package Blogwerk_Util
 * @author Michael Sebel <michael.sebel@blogwerk.com>
 * @copyright Blogwerk AG
 */
class String
{
  /**
   * Regulärer Ausdruck um viele E-Mail Adressen zu validieren.
   * @var string
   */
  const REGEX_EMAIL = '/^[A-Za-z0-9\._\-+]+@[A-Za-z0-9_\-+]+(\.[A-Za-z0-9_\-+]+)+$/';
  /**
   * Regulärer Ausdruck der nur a-z und 0-9 erlaubt.
   * Je nach Anwendung werden A-Z zu a-z konvertiert.
   * @var string
   */
  const ALPHA_NUMERIC = '/[^a-z0-9]/i';
  /**
   * Regulärer Ausdruck der nur a-z und 0-9 erlaubt.
   * Je nach Anwendung werden A-Z zu a-z konvertiert.
   * Weiterhin sind - und _ erlaubt (Für Files gedacht).
   * @var string
   */
  const ALPHA_FILES = '/[^a-z0-9\-\_]/i';
  /**
   * Regulärer Ausdruck der nur a-z und 0-9 erlaubt.
   * Je nach Anwendung werden A-Z zu a-z konvertiert.
   * Weiterhin sind - und _ erlaubt (Für Files gedacht).
   * @var string
   */
  const ALPHA_PATH = '/[^a-z0-9\-\_\/]/i';
  /**
   * Regulärer Ausdruck der a-z,A-Z und 0-9 erlaubt.
   * Weiterhin sind - und _ erlaubt (Für Files gedacht).
   * @var string
   */
  const ALPHA_NUMERIC_LOW = '/[^A-Za-z0-9\-\_]/i';

  /**
   * Mindestlänge eines Strings prüfen
   * @param string $sString zu prüfende Zeichenkette
   * @param integer $nMin minimale Länge
   * @return boolean True, wenn Minimallänge erreicht
   */
  public static function minLength($sString, $nMin)
  {
    $bReturn = false;
    if (strlen($sString) >= $nMin) {
      $bReturn = true;
    }
    return ($bReturn);
  }

  /**
   * Maximale Länge eines Strings prüfen
   * @param string $sString zu prüfende Zeichenkette
   * @param integer $nMax maximale Länge
   * @return boolean True, wenn Maximallänge überschritten
   */
  public static function maxLength($sString, $nMax)
  {
    $bReturn = false;
    if (strlen($sString) >= $nMax) {
      $bReturn = true;
    }
    return ($bReturn);
  }

  /**
   * Maximale und minimale Länge eines Strsings prüfen
   * @param string $sString , zu prüfende Zeichenkette
   * @param integer $nMax maximale Länge
   * @param integer $nMin minimale Länge
   * @return boolean True, wenn String innert max/min ist
   */
  public static function inRange($sString, $nMax, $nMin)
  {
    $bReturn = false;
    // Wenn nicht grösser als Max und kleiner als Min
    if (!self::maxLength($sString, $nMax) && self::minLength($sString, $nMin)) {
      $bReturn = true;
    }
    return ($bReturn);
  }

  /**
   * Max. länge prüfen und abschneiden wenn nötig.
   * @param string $sString zu prüfende Zeichenkette
   * @param integer $nMax maximale Länge des Strings
   * @param boolean $AddDots , True für "..." abschneiden
   * @return string the chopped string
   */
  public static function chopString($sString, $nMax, $AddDots = false)
  {
    if (self::maxLength($sString, $nMax) == true) {
      $sString = substr($sString, 0, $nMax);
      if ($AddDots == true) $sString .= '... ';
    }
    return ($sString);
  }

  /**
   * String auf bestimmte Anzahl Wörter kürzen (the simple way, kein regex)
   * @param string $sString Der zu kürzende String
   * @param int $nWords Anzahl Wörter auf die gekürzt wird
   * @param bool $AddDots Am Ende drei Punkte (Nie, wenn letztes zeichen ein Punkt ist)
   * @return string the chopped string
   */
  public static function chopToWords($sString, $nWords, $AddDots = false)
  {
    // In Wörter Teilen und bis zum maximum oder array Ende wieder zusammenführen
    $sNewString = '';
    $count = 0;
    $words = explode(' ', $sString);
    foreach ($words as $word) {
      $sNewString .= $word . ' ';
      if (++$count == $nWords)
        break;
    }
    // Raustrimmen vom letzten Space
    $sNewString = trim($sNewString);
    // Wenn gewünscht und am Ende kein Punkt, drei Punkte anhängen
    if ($AddDots && substr($sNewString, strlen($sNewString) - 1) !== '.')
      $sNewString .= '...';
    return ($sNewString);
  }

  /**
   * Datumseingabe anhand eines Regex checken.
   * Die dateOps Klasse bietet diverse Regex-Konstanten
   * an um den sFormat Parameter auszufüllen.
   * @param string $sString zu prüfendes Datum
   * @param string $sFormat Regex zur Prüfung
   * @return boolean True, wenn Datum dem gegebenen Format entspricht
   */
  public static function checkDate($sString, $sFormat)
  {
    $bReturn = false;
    if (preg_match($sFormat, $sString)) {
      $bReturn = true;
    }
    return ($bReturn);
  }

  /**
   * Email Adresse validieren.
   * @param string $sString zu validierende Email Adresse
   * @return boolean True, wenn Email Addresse korrekt ist
   */
  public static function checkEmail($sString)
  {
    $bReturn = false;
    if (preg_match(self::REGEX_EMAIL, $sString)) {
      $bReturn = true;
    }
    return ($bReturn);
  }

  /**
   * URL validieren (http, https, ftp)
   * @param string $sUrl zu prüfende URL
   * @return boolean True, wenn URL ok ist
   */
  public static function checkURL($sUrl)
  {
    $bOk = false;
    if (substr($sUrl, 0, 7) == "http://") $bOk = true;
    if (substr($sUrl, 0, 8) == "https://") $bOk = true;
    if (substr($sUrl, 0, 6) == "ftp://") $bOk = true;
    return ($bOk);
  }

  /**
   * Aus gegebener Variable alle Zeichen entfernen.
   * Entfernt wird alles ausser a-z0-9, während
   * Grossbuchstaben zu Kleinbuchstaben konvertiert werden.
   * @param string $Value Zu bearbeitender String
   */
  public static function alphaNumOnly(&$Value)
  {
    $Value = preg_replace(self::ALPHA_NUMERIC, "", $Value);
  }

  /**
   * Diverse bekannte Character mit equivalenten ersetzen die in
   * einer URL angewendet werden können (also mit a-z zeugs)
   * @param string $str Input String
   * @return string geflickter String
   */
  public static function replaceWellKnownChars($str)
  {
    $str = str_replace('ä', 'ae', $str);
    $str = str_replace(array('ö'), 'oe', $str);
    $str = str_replace(array('ü'), 'ue', $str);
    $str = str_replace(array('à', 'â', 'á'), 'a', $str);
    $str = str_replace(array('é', 'ë', 'è', 'ê', '€'), 'e', $str);
    $str = str_replace(array('ï'), 'i', $str);
    $str = str_replace(array('ÿ'), 'y', $str);
    $str = str_replace(array('õ', 'ó', 'ò'), 'o', $str);
    $str = str_replace(array('ñ'), 'n', $str);
    $str = str_replace(array('û', 'ù', 'ú', 'û'), 'u', $str);
    $str = str_replace(array('ç', '¢'), 'c', $str);
    $str = str_replace(array(' ', '_', '+', '&'), '-', $str);
    $str = str_replace(array('\\', '|', '¦'), '/', $str);
    return ($str);
  }

  /**
   * Aus gegebener Variable alle Zeichen entfernen.
   * Entfernt wird alles ausser a-z0-9, während
   * Grossbuchstaben zu Kleinbuchstaben konvertiert werden.
   * Zudem sind "-" und "_" für Files erlaubt. Die
   * Dateiendung wird nicht validiert!
   * @param string $Value Zu bearbeitender Dateiname
   */
  public static function alphaNumFiles(&$Value)
  {
    // Preserven der Dateiendung
    $sExt = strtolower(substr($Value, strripos($Value, '.')));
    $Value = strtolower(substr($Value, 0, strripos($Value, '.')));
    $Value = preg_replace(self::ALPHA_FILES, "", $Value);
    $Value = $Value . $sExt;
  }

  /**
   * Aus gegebener Variable alle Zeichen entfernen.
   * Entfernt wird alles ausser a-zA-Z0-9.
   * Zudem sind "-" und "_" für erlaubt
   * @param string $Value Zu bearbeitender Dateiname
   */
  public static function alphaNumLow(&$Value)
  {
    $Value = preg_replace(self::ALPHA_NUMERIC_LOW, "", $Value);
  }

  /**
   * HTML kodieren, Wert wird für Ausgaben zurückgegeben.
   * @param string $sString Zu kodierender String
   * @return string Kodierter String
   */
  public static function htmlEnt(&$sString)
  {
    $sString = htmlentities($sString);
    return ($sString);
  }

  /**
   * HTML enkodieren und rückkodieren der nur HTML werte.
   * Gedacht für Kodierung von HTML Werten, damit diese
   * direkt in einer View angezeigt werden können.
   * @param string $sString Zu kodierender String
   */
  public static function htmlViewEnt(&$sString)
  {
    $sString = htmlentities($sString);
    $sString = htmlspecialchars_decode($sString);
  }

  /**
   * HTML enkodieren und rückkodieren der nur HTML werte.
   * Gedacht für Kodierung von HTML Werten, damit diese
   * direkt in einer View angezeigt werden können. Liefert
   * den konvertierten Wert noch zurück.
   * @param string $sString Input String
   * @return string Gibt den kodierten String zurück
   */
  public static function htmlViewRet($sString)
  {
    self::htmlViewEnt($sString);
    return ($sString);
  }

  /**
   * HTML Entitäten rückkodieren (z.B. aus dem Editor)
   * @param string $sString zu kodierender String
   */
  public static function htmlEntRev(&$sString)
  {
    $sString = html_entity_decode($sString);
  }

  /**
   * Alles was nach HTML Tags aussieht aus dem String entfernen.
   * @param string $sString zu validierender String
   */
  public static function noHtml(&$sString)
  {
    $sString = strip_tags($sString);
  }

  /**
   * Integer als Boolean validieren (nur 0 / 1).
   * @param mixed $Value Zu validierender Wert
   * @return integer 1 oder 0, je nach Eingabe
   */
  public static function getBoolInt($Value)
  {
    $Value = intval($Value);
    if ($Value > 1 || $Value < 0) {
      $Value = 0;
    }
    return ($Value);
  }

  /**
   * Integer als Boolean validieren (nur 0 / 1).
   * @param mixed $Value Zu validierender Wert
   * @return integer 1 oder alles darunter (auch negative)
   */
  public static function getPosInt($Value)
  {
    $Value = intval($Value);
    if ($Value < 1) {
      $Value = 1;
    }
    return ($Value);
  }

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
   * Kodiert alle nicht alphanumerischen Zeichen in einer URL
   * in die mit % angeführte Hex Version (Referenced)
   * @param string $sUrl zu dekodierender String
   */
  public static function urlEncode(&$sUrl)
  {
    $sUrl = rawurlencode($sUrl);
  }

  /**
   * Gibt die aktuelle URL zurück inkl. https/http und Port/URI
   * @return string Komplette aktuelle URL
   */
  public static function currentUrl()
  {
    $sUrl = 'http';
    if ($_SERVER['HTTPS'] == 'on') $sUrl .= 's';
    $sUrl .= '://' . $_SERVER['SERVER_NAME'];
    if ($_SERVER['SERVER_PORT'] != '80') {
      $sUrl .= ':' . $_SERVER["SERVER_PORT"];
    }
    $sUrl .= $_SERVER['REQUEST_URI'];
    return ($sUrl);
  }

  /**
   * Speichert einen Vardump in eine Variable
   * @param mixed $Var zu dumpende Variable
   * @return string Dump der gegebenen Variable
   */
  public static function getVarDump($Var)
  {
    ob_start();
    var_dump($Var);
    $result = ob_get_contents();
    ob_end_clean();
    return $result;
  }

  /**
   * @param string $content the content of the editor
   * @param string $key the key (name field)
   * @param array $args the editors arguments
   * @return string HTML code to display the wp editor
   */
  public static function getWpEditor($content, $key, $args)
  {
    ob_start();
    wp_editor($content, $key, $args);
    $result = ob_get_contents();
    ob_end_clean();
    return $result;
  }

  /**
   * Nimmt einen Tag und gibt einen Attributeinhalt zurück
   * @param string $sTag HTML Tag
   * @param string $sProperty zu findendes Property
   * @return string Inhalt des Attributs
   */
  public static function parseTagProperty($sTag, $sProperty)
  {
    // Suchen des Properties
    $regex = '/' . $sProperty . '= *([\'][^\'>]*[\']|[""][^"">]*[""])/';
    preg_match_all($regex, $sTag, $result);
    // Property Inhalt ohne Anführungszeichen extrahieren
    $sAttribute = '';
    $nLength = strlen($result[0][0]);
    if ($nLength > 0) {
      $nOffset = strlen($sProperty);
      $sAttribute = substr(
        $result[0][0],
        $nOffset + 2,
        $nLength - ($nOffset + 3)
      );
    }
    return ($sAttribute);
  }

  /**
   * Konvertierung von HTML Code in HTML Entitäten
   * @param string $sString Input
   * @return string Kodierter output
   */
  public static function stringToAsciiEntities($sString)
  {
    $sCoded = '';
    for ($i = 0; $i < strlen($sString); $i++) {
      $sCoded .= '&#' . ord($sString[$i]) . ';';
    }
    return ($sCoded);
  }

  /**
   * Gibt zurück, ob dar angegebene Anfang dem Anfang
   * des zu prüfenden Strings entspricht
   * @param string $sString zu prüfender String
   * @param string $sStart Gewünschter Anfang
   * @return bool true/false ob Entsprechen oder nicht
   */
  public static function startsWith($sString, $sStart)
  {
    $bStartsWith = false;
    $nStartLength = strlen($sStart);
    $nLength = strlen($sString);
    // Prüfen ob der String überhaupt so lang ist wie die Prüfung
    if ($nStartLength <= $nLength) {
      // Entsprechender Teil des gegebenen Strings extrahieren
      $sExtract = substr($sString, 0, $nStartLength);
      // Wenn gleich, dann OK!
      if ($sExtract == $sStart) {
        $bStartsWith = true;
      }
    }
    return ($bStartsWith);
  }

  /**
   * Gibt zurück, ob das angegebene Ende dem Ende
   * des zu prüfenden Strings entspricht
   * @param string $sString zu prüfender String
   * @param string $sEnd Gewünschtes Ende
   * @return bool true/false ob Entsprechen oder nicht
   */
  public static function endsWith($sString, $sEnd)
  {
    $bEndsWith = false;
    $nEndLength = strlen($sEnd);
    $nLength = strlen($sString);
    // Prüfen ob der String überhaupt so lang ist wie die Prüfung
    if ($nEndLength <= $nLength) {
      // Entsprechender Teil des gegebenen Strings extrahieren
      $nStart = $nLength - $nEndLength;
      $sExtract = substr($sString, $nStart);
      // Wenn gleich, dann OK!
      if ($sExtract == $sEnd) {
        $bEndsWith = true;
      }
    }
    return ($bEndsWith);
  }

  /**
   * Gibt einen Zufalls String zurück
   * @param int $nLength , Gewünschte länge
   * @return string, Zufällige Zeichenkette
   */
  public static function getRandom($nLength)
  {
    // Liste möglicher Zahlen
    $sChars = "abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUFVXYZ23456789";
    //Startwert für den Zufallsgenerator festlegen
    $sToken = '';
    for ($nChars = 0; $nChars < $nLength; $nChars++) {
      $sToken .= $sChars[mt_rand(0, 55)];
    }
    return ($sToken);
  }



  /**
   * Performs simple templating tasks typically used for SQL Statements.
   * Such a template will have placeholders with curly braces. Theses placeholders will be replaced by the values from the parameters array,
   * where the key in the parameters array corresponds to the placeholder. The order of the placeholder does not matter,
   * even when nested (i.e. a placeholder in a parameter value, where the nested placeholder can be defined in the parameters array)
   *
   * See test/PrepareSqlTemplateTest.php for PHPUnit tests.
   *
   * @param string $sqlTemplate SQL Template: SQL String with {placeholder}'s, which will be replaced by the corresponding value from the associative $parameters array
   * @param array $parameters associative array where the key is the parameter name and the value is the value for that parameter (string, int, array of strings, array of ints)
   * @param bool $stripEmptyParameters
   * @return string prepared SQL
   */
  public static function prepareSql($sqlTemplate, array $parameters, $stripEmptyParameters = false)
  {
    $sql = $sqlTemplate;
    $foundPlaceholders = array();

    // trick to get all placeholders from the template and the $parameters in one string ($parameters can hold values with placeholders)
    $placeholderHaystack = $sqlTemplate . var_export($parameters, true);

    $placeholderHaystackLength = strlen($placeholderHaystack);

    // loop through the haystack and extract {placeholder}'s
    for ($pos = strpos($placeholderHaystack, '{'); $pos !== false && $pos < $placeholderHaystackLength; $pos = strpos($placeholderHaystack, '{', $pos + 1)) {
      if ($placeholderHaystack[$pos] == '{') {
        $placeholderStart = ++$pos;
        $placeholderEnd = strpos($placeholderHaystack, '}', $pos);
        if ($placeholderEnd !== false) {
          $foundPlaceholders[] = substr($placeholderHaystack, $placeholderStart, $placeholderEnd - $placeholderStart);
        }
      }
    }

    // loop through all found placeholders from the template and try to replace them with values from $parameters
    foreach (array_unique($foundPlaceholders) as $foundPlaceholder) {

      // determine if a modifier is used
      if (strpos($foundPlaceholder, ':') !== false) {
        list($parameterName, $modifier) = array_reverse(explode(':', $foundPlaceholder));
      } else {
        $parameterName = $foundPlaceholder;
        $modifier = false;
      }

      // only do something if the key is defined in $parameters (i.e. isset, instead of array_key_exists, returns false with a null value...)
      if (array_key_exists($parameterName, $parameters)) {
        $modifiedParameterValue = $parameters[$parameterName];
        // apply the modifiers
        switch ($modifier) {
          case 'sql':
          case 'raw':
            break;
          case 'quote':
          case 'escape':
          default:
            $modifiedParameterValue = self::escapeVar($modifiedParameterValue);
            break;
        }

        // implode if the parameter value is an array
        if (is_array($modifiedParameterValue)) {
          $modifiedParameterValue = implode(',', $modifiedParameterValue);
        }

        // actual replacement
        $sql = str_replace('{' . $foundPlaceholder . '}', $modifiedParameterValue, $sql);
      } else if ($stripEmptyParameters) {

        // if the placeholder does not exist in the parameter array, it will be stripped out, but only with $stripEmptyParameters=true (default is false)
        $sql = str_replace('{' . $foundPlaceholder . '}', '', $sql);
      }
    }

    // ltrim all lines (not required, but nice for phpunit)
    $sqlLines = explode(PHP_EOL, $sql);
    $sqlLines = array_map('ltrim', $sqlLines);
    $sql = implode(PHP_EOL, $sqlLines);
    return $sql;
  }

  /**
   * Escapes and wraps a $variable (string|array of strings)
   * @param array|string $var variable which will be wrapped in double-quotes and escaped
   * @return array|string
   */
  public static function escapeVar($var)
  {
    if (is_array($var)) {
      foreach ($var as $varKey => $varField) {

        // recursive escaping
        $var[$varKey] = static::escapeVar($varField);
      }
    } elseif (is_string($var)) {
      $var = '"' . mysql_real_escape_string($var) . '"';
    } else {
      // what else ??
    }
    return $var;
  }

  public static function getInnerHTML($Node)
  {
    $Body = $Node->ownerDocument->documentElement->firstChild->firstChild;
    $Document = new DOMDocument();
    $Document->appendChild($Document->importNode($Body, true));
    return $Document->saveHTML();
  }

  /**
   * transforms html code with paragraphs to remove the paragraphs but adds a
   * double break after every legitimate paragraph. used i.e. for newsletter.
   * straightforward function, can break your html code if it's not valid.
   * @param string $html the html code to be converte
   * @return string the converted html code
   */
  public static function paragraphToDoubleBreak($html)
  {
    $encodedHtml = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
    $doc = new DOMDocument();
    //need a root element
    $doc->loadHTML('<body>' . $encodedHtml . '</body>');

    $xpath = new DOMXpath($doc);
    //xpath
    $paragraphs = $xpath->query('//p'); // http://cdn.memegenerator.net/instances/400x/34894660.jpg
    $maxEndingBreakTags = 2;
    $paragraphIndex = 0;
    foreach ($paragraphs as $paragraph) {
      $paragraphIndex++;
      //use fragment for flexibility
      $fragment = $doc->createDocumentFragment();
      $breakTagCount = 0;
      $paragraphChildIndex = 0;
      foreach ($paragraph->childNodes as $childNode) {
        $append = true;
        if ($childNode->nodeName == 'br') {
          if ($paragraphChildIndex < $paragraph->childNodes->length - 1) {
            $nextNode = $paragraph->childNodes->item($paragraphChildIndex + 1)->nodeValue;
            if (substr($nextNode, 0, 1) == "\n" && $breakTagCount > 0) {
              $append = false;
            }
          }
          $breakTagCount++;
          if ($breakTagCount > $maxEndingBreakTags) {
            $append = false;
          }
        } else {
          $breakTagCount = 0; //reset break Tag count: only count the last br's in a paragraph
        }

        if ($append == true) {
          $fragment->appendXML($doc->saveXML($childNode));
        }
        $paragraphChildIndex++;
      }
      //only append breaks, if they weren't present and if it's not the last paragraph
      if ($paragraphIndex != $paragraphs->length) {

        for ($appendIndex = $maxEndingBreakTags - $breakTagCount; $appendIndex > 0; $appendIndex--) {
          $fragment->appendXML('<br />');
        }
      }
      //DOMDocumentFragment is also a DOMNode, so replacing it will work
      $paragraph->parentNode->replaceChild($fragment, $paragraph);
    }
    //get rid of previously used root element
    $new_html = '';
    $bodyNode = $doc->getElementsByTagName('body')->item(0);
    foreach ($bodyNode->childNodes as $childNode) {
      $new_html .= $doc->saveXML($childNode);
    }

    return $new_html;
  }

  static public function convertToString($value)
  {
    if (is_float($value)) {
      $result = str_replace(',', '.', (string)$value);
    } else {
      $result = (string)$value;
    }

    return $result;
  }


}