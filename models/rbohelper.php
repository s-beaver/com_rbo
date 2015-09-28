<?php
class RbOHelper {
  // =================================================================
  static function checkAccess() {
    $user = JFactory::getUser ();
    $can_access = ! (array_search ("8", $user->groups) === false &&
         array_search ("7", $user->groups) === false && array_search ("6", $user->groups) === false);
    if (! $can_access) {
      JLog::add ("Доступ запрещен для " . $user->name, JLog::ERROR, 'com_rbo');
      echo ("Access denied for " . $user->name);
      header ('Refresh: 3; URL=http://robik.ru/');
      exit ();
    }
  }
  
  // =================================================================
  static function getTimeZone() {
    $userTz = JFactory::getUser ()->getParam ('timezone');
    $timeZone = JFactory::getConfig ()->getValue ('offset');
    if ($userTz) {
      $timeZone = $userTz;
    }
    return new DateTimeZone ($timeZone);
  }
  
  // =================================================================
  static function getCurrentTimeForDb() {
    $tz = self::getTimezone ();
    $currentTime = new JDate ("now", $tz);
    return $currentTime->format ('d.m.Y H:i:00', true); // https://php.net/manual/en/function.date.php время добавить - скорректировать timezone
  }
  
  // =================================================================
  static function sendEMail($subj, $body) {
    $mailer = & JFactory::getMailer ();
    $config = & JFactory::getConfig ();
    $mailer->setSender (
        array ($config->getValue ('config.mailfrom'),$config->getValue ('config.fromname') ));
    $mailer->addRecipient (RbOConfig::$documentNotifyEMails);
    $mailer->setSubject ($subj);
    $mailer->setBody ($body);
    $mailer->CharSet = "utf8";
    $send = & $mailer->Send ();
    if ($send !== true) {
      JLog::add ("Ошибка при отправке почты " . $send->message, JLog::ERROR, 'com_rbo');
    }
  }

}

