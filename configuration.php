<?php
class RbOConfig {
  public static $firms = array (
      "ИП" => array ("f_name" => "ИП Ефремов Сергей Александрович",
          "f_fullname" => "Индивидуальный предприниматель Ефремов Сергей Александрович",
          "f_inn" => "667204292729","f_kpp" => "","f_okpo" => "0152102248",
          "f_addr" => "620142 г. Екатеринбург, ул. Белинского 147-29",
          "f_ogrn" => "ОГРНИП 306967227500020","f_phone" => "+7(343)207-3009",
          "f_bank" => "ПАО КБ 'УБРИР', Г. ЕКАТЕРИНБУРГ","f_bik" => "046577795",
          "f_rch" => "30101810900000000795","f_kch" => "40802810962130000081",
          "f_stamp" => "icon-st-ip.jpg" ),

      "ИП-нал" => array ("copyof" => "ИП"),
      "ИП-бн" => array ("copyof" => "ИП"),
      
      "ООО" => array ("f_name" => "ООО 'РОБИК.РУ'",
          "f_fullname" => "Общество с ограниченной ответственностью 'РОБИК.РУ'",
          "f_inn" => "6672310290","f_kpp" => "667201001","f_okpo" => "65612151",
          "f_addr" => "620142 г. Екатеринбург, ул. Белинского 147-29","f_ogrn" => "",
          "f_phone" => "+7(343)207-3009","f_bank" => "ПАО КБ 'УБРИР', Г. ЕКАТЕРИНБУРГ",
          "f_bik" => "046577795","f_rch" => "30101810900000000795","f_kch" => "40702810962410000186",
          "f_stamp" => "icon-st-ooo.jpg" ) );
  public static $managers = array ("Аня" => "Меньшенина Анна","Володя" => "Николашвили Владимир",
      "Алексей" => "Вяткин Алексей","Николай" => "Грозных Николай" );
  public static $documentNotifyEMails = array ("s_efremov@mail.ru" );
  public static $pathRemoveForJsScrips = '/var/www/robik.ru/';
  public static $prefixForDBTables = '';
  public static $suffixForDBTables = '';
}