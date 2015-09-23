<?php
/**
 * Класс для подключения файлов.
 *
 * @author Sergey Novikov <Novikov.Sergey.S 0_0 GMail.Com>
 */
final class Includer {
  /**
   * @var string Имя подключаемого файла.
   */
  private static $path = '';


  /**
   * @var bool Подключить файл однократно.
   */
  private static $isIncludeOnce = false;


  /**
   * @var array Массив экспортируемых переменных.
   */
  private static $export = array();


  /**
   * @var mixed Настоящий результат подключения файла.
  */
  private static $return = null;


  /**
   * Многократное некритичное подключение файла.
   *
   * @param string Имя подключаемого файла.
   * @param array Массив экспортируемых переменных.
   * @param &mixed Контейнер, в котором будут сохранены полученные из подключённого файла переменные.
   * @param &mixed Контейнер, в котором будет сохранён настоящий результат подключения файла.
   * @return bool Файл подключён успешно.
   */
  public static function includeFile($path, array $export = array(), & $import = null, & $return = null) {
    return self::basicInclude($path, false, false, $export, $import, $return);
  }


  /**
   * Однократное некритичное подключение файла.
   *
   * @param string Имя подключаемого файла.
   * @param array Массив экспортируемых переменных.
   * @param &mixed Контейнер, в котором будут сохранены полученные из подключённого файла переменные.
   * @param &mixed Контейнер, в котором будет сохранён настоящий результат подключения файла.
   * @return bool Файл подключён успешно.
   */
  public static function includeOnce($path, array $export = array(), & $import = null, & $return = null) {
    return self::basicInclude($path, true, false, $export, $import, $return);
  }


  /**
   * Многократное критичное подключение файла.
   *
   * @param string Имя подключаемого файла.
   * @param array Массив экспортируемых переменных.
   * @param &mixed Контейнер, в котором будут сохранены полученные из подключённого файла переменные.
   * @return mixed Настоящий результат подключения файла.
   */
  public static function requireFile($path, array $export = array(), & $import = null) {
    $return = null;
    self::basicInclude($path, false, true, $export, $import, $return);
    return $return;
  }


  /**
   * Однократное критичное подключение файла.
   *
   * @param string Имя подключаемого файла.
   * @param array Массив экспортируемых переменных.
   * @param &mixed Контейнер, в котором будут сохранены полученные из подключённого файла переменные.
   * @return mixed Настоящий результат подключения файла.
   */
  public static function requireOnce($path, array $export = array(), & $import = null) {
    $return = null;
    self::basicInclude($path, true, true, $export, $import, $return);
    return $return;
  }


  /**
   * Подключение файла.
   *
   * @param string Имя подключаемого файла.
   * @param bool Подключить файл однократно.
   * @param bool Эмулировать require.
   * @param array Массив экспортируемых переменных.
   * @param &mixed Контейнер, в котором будут сохранены полученные из подключённого файла переменные.
   * @param &mixed Контейнер, в котором будет сохранён настоящий результат подключения файла.
   * @return bool Подключение успешно.
   */
  private static function basicInclude($path, $isIncludeOnce, $isRequire, array $export, & $import, & $return) {
    // Сохранение параметров подключения.
    self::$path = $path;
    self::$isIncludeOnce = $isIncludeOnce;
    self::$export = $export;

    // Попытка подключения файла.
    try {
      $import = self::safeInclude();
      $return = self::$return;
    } catch (ErrorException $exception) {
      if ($exception->getFile() !== __FILE__) {
        // Ошибка в подключённом файле.
        throw $exception;
      } elseif ($isRequire) {
        // Подключение неуспешно (эмуляция require).
        // Можно не вбрасывать исключение, а передать его прямо в обработчик исключений и завершить работу.
        throw new Exception("File not included: $path", 0, $exception);
      } else {
        // Файл не был подключён.
        return false;
      }
    }

    // Файл подключён успешно.
    return true;
  }


  /**
   * Безопасное подключение файла.
   *
   * @return array Массив полученных из подключённого файла переменных.
   */
  private static function safeInclude() {
    // Экспорт выбранных переменных в текущую область видимости перед подключением файла.
    extract(self::$export);

    // Подключение файла.
    // Результат сохраняется как член класса, поскольку подключаемый файл мог создать переменную с таким же именем.
    if (self::$isIncludeOnce) {
      self::$return = include_once self::$path;
    } else {
      self::$return = include self::$path;
    }

    // Получение массива новых переменных.
    $import = get_defined_vars();
    unset($import['GLOBALS']);
    unset($import['_GET']);
    unset($import['_POST']);
    unset($import['_COOKIE']);
    unset($import['_FILES']);
    unset($import['_REQUEST']);
    unset($import['_ENV']);
    unset($import['_SERVER']);
    unset($import['_SESSION']);

    // Возврат массива полученных из подключённого файла переменных.
    return $import;
  }
}