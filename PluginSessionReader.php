<?php
/**
 * Read content of session file.
 * 
 * PluginSessionReader::unserialize('content from session file...');
 */
class PluginSessionReader {
  private static function handler_phpbinary($content) {
    $array = array();
    $offset = 0;
    while ($offset < strlen($content)) {
      $num = ord($content[$offset]);
      $offset += 1;
      $varname = substr($content, $offset, $num);
      $offset += $num;
      $data = unserialize(substr($content, $offset));
      $array[$varname] = $data;
      $offset += strlen(serialize($data));
    }
    return $array;
  }
  private static function handler_php($content) {
    $array = array();
    $offset = 0;
    while ($offset < strlen($content)) {
      if (!strstr(substr($content, $offset), "|")) {
        throw new Exception("PluginSessinReader says: Invalid data (".substr($content, $offset).").");
      }
      $pos = strpos($content, "|", $offset);
      $num = $pos - $offset;
      $varname = substr($content, $offset, $num);
      $offset += $num + 1;
      $data = unserialize(substr($content, $offset));
      $array[$varname] = $data;
      $offset += strlen(serialize($data));
    }
    return $array;
  }
  public static function unserialize($content) {
    $serialize_handler = ini_get("session.serialize_handler");
    switch ($serialize_handler) {
      case "php":
        return self::handler_php($content);
        break;
      case "php_binary":
        return self::hanlder_phpbinary($content);
        break;
      default:
        throw new Exception("PluginSessinReader says: Serialize handler ".$serialize_handler." not supported.");
    }
  }
}