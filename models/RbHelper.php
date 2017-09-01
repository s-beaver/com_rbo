<?php

class RbHelper
{
    // =================================================================
    static function checkAccess()
    {
        $user = JFactory::getUser();
        $can_access = !(array_search("8", $user->groups) === false &&
            array_search("7", $user->groups) === false && array_search("6", $user->groups) === false);
        if (!$can_access) {
            JLog::add("Доступ запрещен для " . $user->name, JLog::ERROR, 'com_rbo');
            echo("Access denied for " . $user->name);
            header('Refresh: 3; URL=http://robik.ru/');
            exit ();
        }
    }

    // =================================================================
    static function getTimeZone_before_12_1()
    {
        $userTz = JFactory::getUser()->getParam('timezone');
        $timeZone = JFactory::getConfig()->getValue('offset');
        if ($userTz) {
            $timeZone = $userTz;
        }
        return new DateTimeZone ($timeZone);
    }

    // =================================================================
    static function getTimeZone()
    {
        return new DateTimeZone(JFactory::getConfig()->get('offset'));
    }

    // =================================================================
    static function getCurrentTimeForDb()
    {
        $tz = self::getTimezone();
        $currentTime = new JDate ("now", $tz);
        return $currentTime->format('d.m.Y H:i:00', true); // https://php.net/manual/en/function.date.php время добавить - скорректировать timezone
    }

    // =================================================================
    static function sendEMail($subj, $body)
    {
        $mailer = &JFactory::getMailer();
        $mailer->setSender(array("max@robik.ru", "Robik.ru"));
        $mailer->addRecipient(RbConfig::$documentNotifyEMails);
        $mailer->setSubject($subj);
        $mailer->setBody($body);
        //$mailer->CharSet = "utf8";
        $send = &$mailer->Send();
        if ($send !== true) {
            JLog::add("Ошибка при отправке почты " . $send->message, JLog::ERROR, 'com_rbo');
            return false;
        }
        return true;
    }

    // =================================================================
    static function getVersion()
    {
        return file_get_contents(RBO_PATH . '/.version');
    }

    // =================================================================
    static function translit($s)
    {
        if (empty($s)) return "";
        $del = array("ь", "ъ", ",", ":", "\"", "'", "!", "#", "@", "%", "&", "?", "*", "(", ")", "{", "}", "[", "]", "\\", "/", ";", "<", ">", "|", "+", "-");
        $rus = array("  ", " ", "а", "б", "в", "г", "д", "е", "ё", "ж", "з", "и", "й", "к", "л", "м", "н", "о", "п", "р", "с", "т", "у", "ф", "х", "ц", "ч", "ш", "щ", "ы", "э", "ю", "я", "№");
        $eng = array(" ", "_", "a", "b", "v", "g", "d", "e", "e", "j", "z", "i", "i", "k", "l", "m", "n", "o", "p", "r", "s", "t", "u", "f", "h", "c", "ch", "sh", "sh", "i", "e", "u", "ya", "N");

        $res = str_replace($del, "", mb_strtolower($s));
        $res = str_replace($rus, $eng, $res);
        return $res;
    }

// =================================================================
    static function getTableName($table_name)
    {
        if (RbConfig::$useJoomlaPrefixForDBTables) {
            $jConfig = new JConfig();
            return $jConfig->dbprefix . $table_name . RbConfig::$suffixForDBTables;
        } else
            return RbConfig::$prefixForDBTables . $table_name . RbConfig::$suffixForDBTables;
    }

// =================================================================
    static function executeQuery($SQL)
    {
        $db = JFactory::getDBO();
        $db->setQuery($SQL);
        $result = $db->execute();
        return $result;
    }

// =================================================================
    static function insertQuery($SQL)
    {
        $db = JFactory::getDBO();
        $db->setQuery($SQL);
        $result = $db->execute();
        if (!$result) return null;
        return $db->insertid();
    }

// =================================================================
    static function SQLGet($SQL)
    {
        $db = JFactory::getDBO();
        $db->setQuery($SQL);
        $result = $db->execute();
        if (!$result) return null;
        return $db->loadResult();
    }

// =================================================================
    static function SQLGetAssocList($SQL)
    {
        $db = JFactory::getDBO();
        $db->setQuery($SQL);
        $result = $db->execute();
        if (!$result) return null;
        return $db->loadAssocList();
    }

}

