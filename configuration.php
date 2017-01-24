<?php

class RbConfig
{
        public static $firms = array(
        "ИП" => array("f_name" => "ИП Ефремов Сергей Александрович",
            "f_fullname" => "Индивидуальный предприниматель Ефремов Сергей Александрович",
            "f_inn" => "667204292729", "f_kpp" => "", "f_okpo" => "0152102248",
            "f_addr" => "620072 г. Екатеринбург, ул. Рассветная 9а-110",
            "f_ogrn" => "ОГРНИП 306967227500020", "f_phone" => "+7(343)207-3009",
            "f_bank" => "ПАО КБ 'УБРИР', Г. ЕКАТЕРИНБУРГ", "f_bik" => "046577795",
            "f_rch" => "30101810900000000795", "f_kch" => "40802810962130000081",
            "f_stamp" => "icon-st-ip.jpg"),

        "ИП-НАЛ" => array("copyof" => "ИП"),
        "ИП-БН" => array("copyof" => "ИП"),

        "ООО" => array("f_name" => "ООО 'РОБИК.РУ'",
            "f_fullname" => "Общество с ограниченной ответственностью 'РОБИК.РУ'",
            "f_inn" => "6672310290", "f_kpp" => "667001001", "f_okpo" => "65612151",
            "f_addr" => "620137 г. Екатеринбург, ул. Сулимова 50-0.24", "f_ogrn" => "1106672002315",
            "f_phone" => "+7(343)207-3009", "f_bank" => "ПАО КБ 'УБРИР', Г. ЕКАТЕРИНБУРГ",
            "f_bik" => "046577795", "f_rch" => "30101810900000000795", "f_kch" => "40702810962410000186",
            "f_stamp" => "icon-st-ooo.jpg"));
    public static $managers = array("Аня" => "Меньшенина Анна", "Володя" => "Николашвили Владимир",
        "Алексей" => "Вяткин Алексей", "Николай" => "Грозных Николай", "Сергей" => "Ефремов Сергей");
    public static $documentNotifyEMails = array("s_efremov@mail.ru","asv_@mail.ru");
    public static $useJoomlaPrefixForDBTables = true;
    public static $prefixForDBTables = '';
    public static $suffixForDBTables = '';
//    public static $currentPriceName = '20160101';

    /*Сквозная нумерация для документов: Нумерация прибавляется на 1 с каждым новым документом, независимо от его типа.
    Если же акты/накладные создаются на основании счетов, то они приобретают номера счетов*/
    public static $continuousNumbering = true;

    public static $operstype = array(
        "закуп" => array("signMove" => 1),
        "продажа" => array("signMove" => -1),
        "списание" => array("signMove" => -1),
        "затраты-бухгал" => array("signMove" => -1),
        "затраты-налоги" => array("signMove" => -1),
        "затраты-прочие" => array("signMove" => -1),
        "затраты-зарплата" => array("signMove" => -1),
        "затраты-банков" => array("signMove" => -1),
        "затраты-произв" => array("signMove" => -1),
        "затраты-аренда" => array("signMove" => -1),
        "затраты-коммун" => array("signMove" => -1),
        "затраты-связь" => array("signMove" => -1),
        "ддс" => array("signMove" => 0)
    );
}