<?
define("INCLUDE_DIR", dirname(__FILE__) . "/nologostudio/");
define("PROJECT_NAME", "ОхотАктив");

define("IBLOCK_NEWS_ID", 3);
define("IBLOCK_SHOP_ID", 4);
define("IBLOCK_CITY_ID", 6);
define("IBLOCK_CATALOG_ID", 10);
define("IBLOCK_COMPLECT_ID", 11);
define("IBLOCK_REVIEWS_ID", 12);
define("IBLOCK_SETTINGS_ID", 1);
define("IBLOCK_SALE_ID", 14);

define("AVAILABLE_PROP_ID", 98);
define("LABELS_PROP_ID", 42);
define("ITEMS_PROP_ID", 73);
define("PHOTO_PROP_ID", 26);
define("LABELS_PROP_PHOTO_ID", 37239);
define("LABELS_PROP_COMPLECT_ID", 37238);

define("SELFDELIVERY_ID", 2);

define("FASTORDER_USER_ID", 9);
define("BX_CRONTAB_SUPPORT", false);
define("BASE_PRICE_ID", 1);
define("SUBSCRIBE_RUB_ID", 1);

CModule::IncludeModule('nologostudio.main');

require_once INCLUDE_DIR . "functions.php";
require_once INCLUDE_DIR . "CTemplate.php";
require_once INCLUDE_DIR . "CEvents.php";

AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", Array("\NLS\CEvents", "OnBeforeIBlockElementUpdate"));

//ORM
require_once INCLUDE_DIR . "ORM_ohota_favorite_items.php";
require_once INCLUDE_DIR . "ORM_ohota_items_rests.php";

require_once(dirname(__FILE__) . '/include/seo/filterMeta.php');
// poddomenator class
// работа с поддоменами
    include $_SERVER['DOCUMENT_ROOT']."/local/php_interface/include/seo/poddomenator.php";

//устанавливаем переменную поддомена
  poddomenator::DomenSet();


global $actionFilter, $actionFooterFilter, $sectionFooterFilter, $newsFilter, $arViewedFilter, $arMainBestFilter, $arCatalogFilter;
$actionFilter = array(
	'ACTIVE' => 'Y',
	'PROPERTY_SHOW_ON_MAIN_VALUE' => 'Y',
	'TAGS' => '%акция%',
);
$actionFooterFilter = array(
	'ACTIVE' => 'Y',
	'TAGS' => '%акция%',
);
$sectionFooterFilter = array(
);

$newsFilter = array(
	'ACTIVE' => 'Y',
	'PROPERTY_SHOW_ON_MAIN_VALUE' => 'Y',
	'!TAGS' => ['%акция%']
);

$arMainBestFilter = array(
	'ACTIVE' => 'Y',
	'PROPERTY_LABELS' => 37236
);
$arCatalogFilter = array(
	'!CATALOG_PRICE_1' => false
);
$arViewed = unserialize($APPLICATION->get_cookie("VIEWED_ITEMS"));
if(!is_array($arViewed) or count($arViewed) == 0) {
	$arViewed = false;
}
$arViewedFilter = array(
	'ID' => $arViewed
);
global $arShopFilter;
$arShopFilter = array();

AddEventHandler('iblock', 'OnBeforeIBlockElementAdd', 'IBElementCreateHandler');

function IBElementCreateHandler(&$arFields) {
  $SITE_ID = 's1';                         // идентификатор сайта
  $IBLOCK_ID = 8;                         // ID нужного инфоблока
  $EVENT_TYPE = 'WF_NEW_IBLOCK_ELEMENT'; // тип почтового шаблона
  if($arFields['IBLOCK_ID']==$IBLOCK_ID) {
    $arMailFields = array(
      "NAME" => $arFields['NAME'],
		"PREVIEW_TEXT" => $arFields["PREVIEW_TEXT"],
		"EMAIL" => $arFields["CODE"]
    );
    CEvent::Send($EVENT_TYPE, $SITE_ID, $arMailFields);
  }
}

function NLSSettings_GetSettings() {
	return array(
		"TEMPLATE" => array(
			"NAME" => "Шаблон",
			"VARS" => array(
				"nls_header_img_default" => array(
					"NAME" => "Шаблон: Фон заголовка по умолчанию",
					"TYPE" => "FILE",
					"DEFAULT" => ""
				),
			)
		),
		"NEWS" => array(
			"NAME" => "Новости",
			"VARS" => array(
				"nls_news_tags_cnt" => array(
					"NAME" => "Новости: Количество тегов",
					"DEFAULT" => ""
				),
				"nls_news_recommendation_cnt" => array(
					"NAME" => "Новости: Количество похожих новостей на детальной",
					"DEFAULT" => ""
				),
				"nls_news_widget_share" => array(
					"NAME" => "Новости: Виджет шары",
					"TYPE" => "TEXTAREA",
					"DEFAULT" => ""
				),
				"nls_news_widget_comments" => array(
					"NAME" => "Новости: Виджет комментариев",
					"TYPE" => "TEXTAREA",
					"DEFAULT" => ""
				),
			)
		),
		"SOCIAL" => array(
			"NAME" => "Ссылки на социальные сети",
			"VARS" => array(
				"nls_social_fb" => array(
					"NAME" => "Facebook",
					"DEFAULT" => ""
				),
				"nls_social_vk" => array(
					"NAME" => "ВКонтакте",
					"DEFAULT" => ""
				),
				"nls_social_yt" => array(
					"NAME" => "YouTube",
					"DEFAULT" => ""
				),
			)
		),
		"MAIN" => array(
			"NAME" => "Главная страница",
			"VARS" => array(
				"nls_main_title" => array(
					"NAME" => "Акция по умолчанию: Заголовок",
					"DEFAULT" => ""
				),
				"nls_main_text" => array(
					"NAME" => "Акция по умолчанию: Текст",
					"TYPE" => "TEXTAREA",
					"DEFAULT" => ""
				),
				"nls_main_image" => array(
					"NAME" => "Акция по умолчанию: Изображение",
					"TYPE" => "FILE",
					"DEFAULT" => ""
				),
			)
		),
	);
}

function deleteOldBaskets(){
    if ( CModule::IncludeModule("sale") && CModule::IncludeModule("catalog") ){
        global $DB;
        $nDays = 10; // сроком старше 10дней
        $nDays = IntVal($nDays);
        $strSql =
            "SELECT f.ID ".
            "FROM b_sale_fuser f ".
            "LEFT JOIN b_sale_order o ON (o.USER_ID = f.USER_ID) ".
            "WHERE ".
            "   TO_DAYS(f.DATE_UPDATE)<(TO_DAYS(NOW())-".$nDays.") ".
            "   AND o.ID is null ".
            "   AND f.USER_ID is null ".
            "LIMIT 3000";
        $db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
        while ($ar_res = $db_res->Fetch()){
            CSaleBasket::DeleteAll($ar_res["ID"], false);
            CSaleUser::Delete($ar_res["ID"]);
        }
    }
    return "deleteOldBaskets();";
}
// horse // horse // horse // horse // horse // horse // horse // horse // horse // horse // horse // horse

// Убираю выгрузку описаний и наименований товаров из 1С
AddEventHandler("iblock", "OnBeforeIBlockElementUpdate","DoNotUpdate");
function DoNotUpdate(&$arFields)
{
    if ($_REQUEST['mode']=='import')
    {
		unset($arFields['NAME']);
		unset($arFields['PREVIEW_TEXT']);
		unset($arFields['DETAIL_TEXT']);
	}
}
/* AddEventHandler("iblock", "OnBeforeIBlockElementAdd","DoNotAdd");
function DoNotAdd(&$arFields)
{
   if ($arFields['NAME'] !== '') {

   }
} */

//Товар в нескольких категориях

AddEventHandler("iblock", "OnBeforeIBlockElementUpdate","SaveMySection");
function SaveMySection(&$arFields)
{
    if (@$_REQUEST['mode']=='import') // 1C?
    {
        $db_old_groups = CIBlockElement::GetElementGroups($arFields['ID'], true);
        while($ar_group = $db_old_groups->Fetch())
        {
            if(!in_array($ar_group['ID'],$arFields['IBLOCK_SECTION']))
            $arFields['IBLOCK_SECTION'][]=$ar_group['ID'];
        }
    }
}

// canonicals
AddEventHandler("iblock", "OnAfterIBlockElementUpdate", "Canonicals");
AddEventHandler("iblock", "OnAfterIBlockElementAdd", "Canonicals");

function Canonicals(&$arFields) {

CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");
CModule::IncludeModule('sale');

 /*$el = new CIBlockElement; */

$arFilter = Array(
 "IBLOCK_ID"=>10, "ACTIVE"=>"Y", "!PROPERTY_SRODITELKHARAKTERISTIKIDLYASAYTA"=>false, "ID"=>$arFields['ID']
 );
$res = CIBlockElement::GetList(Array(), $arFilter, Array("ID", "PROPERTY_SRODITELKHARAKTERISTIKIDLYASAYTA"));
		$ar_fields = $res->GetNext();
		$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>10, "ID"=>$ar_fields['PROPERTY_SRODITELKHARAKTERISTIKIDLYASAYTA_ENUM_ID'], "CODE"=>"SRODITELKHARAKTERISTIKIDLYASAYTA"));
		$enum_fields = $property_enums->GetNext();

		$arFilter2 = Array(
		"IBLOCK_ID"=>10, "ACTIVE"=>"Y", "XML_ID"=>$enum_fields['XML_ID']
		);
		$res = CIBlockElement::GetList(Array(), $arFilter2, Array("ID", "PARENT")); // получил предка
		$ar_fields = $res->GetNext();

			$el_res= CIBlockElement::GetByID( $ar_fields['ID'] ); 
			$el_arr = $el_res->GetNext();

				$arLoadProductArray = Array(
				"PROPERTY_VALUES"=>array(
				"CANONICAL"=>$el_arr[ 'DETAIL_PAGE_URL' ],
				),	
  				);
  
				if($arFields['ID'] !== $ar_fields['ID']) {
				CIBlockElement::SetPropertyValuesEx($arFields['ID'], 10, array('CANONICAL' => $el_arr[ 'DETAIL_PAGE_URL' ])); // поставил каноникал текучке
				CIBlockElement::SetPropertyValuesEx($ar_fields['ID'], 10, array('CANONICAL' => $el_arr[ 'DETAIL_PAGE_URL' ])); // поставил каноникал паренту
				/*$res = $el->Update($arFields['ID'], $arLoadProductArray);
				$res = $el->Update($ar_fields['ID'], $arLoadProductArray);*/
				}
				
/* ************option********** */
				/*$enum_list = CIBlockPropertyEnum::GetList(Array(), Array("IBLOCK_ID"=>10, "CODE"=>"SRODITELKHARAKTERISTIKIDLYASAYTA", "XML_ID"=>$enum_fields['XML_ID'])); 

				$arEnumIsMain = $enum_list->GetNext();
				$EnumID = $arEnumIsMain ["ID"];

				$arFilter3 = Array(
				"IBLOCK_ID"=>10, "ACTIVE"=>"Y", array("PROPERTY"=>array("SRODITELKHARAKTERISTIKIDLYASAYTA"=>$EnumID)) // Все товары с таким enum
				);

				$res = CIBlockElement::GetList(Array(), $arFilter3, Array("ID"));

				while ($ar_fields = $res->GetNext()) {
				if ($ar_fields['ID'] != $arFields['ID']) // Кроме себя самого 2
				$child_string .= $ar_fields['ID'].", ";
				}

				CIBlockElement::SetPropertyValuesEx($arFields['ID'], 10, array('PARENT' => $child_string)); // поставил предка ребенку

				$arLoadProductArrayParent = Array(
				"PROPERTY_VALUES"=>array(	
				"PARENT"=>$child_string,
				),	
  				);
*/
				/*$res = $el->Update($arFields['ID'], $arLoadProductArrayParent);*/			
				
}



AddEventHandler("main", "OnAfterUserAdd", "OnAfterUserRegisterHandler");
AddEventHandler("main", "OnAfterUserRegister", "OnAfterUserRegisterHandler");
function OnAfterUserRegisterHandler(&$arFields)
{
   if (intval($arFields["ID"])>0)
   {
      $toSend = Array();
      $toSend["PASSWORD"] = $arFields["CONFIRM_PASSWORD"];
      $toSend["EMAIL"] = $arFields["EMAIL"];
      $toSend["USER_ID"] = $arFields["ID"];
      $toSend["USER_IP"] = $arFields["USER_IP"];
      $toSend["USER_HOST"] = $arFields["USER_HOST"];
      $toSend["LOGIN"] = $arFields["LOGIN"];
      $toSend["NAME"] = (trim ($arFields["NAME"]) == "")? $toSend["NAME"] = htmlspecialchars('<Не указано>'): $arFields["NAME"];
      $toSend["LAST_NAME"] = (trim ($arFields["LAST_NAME"]) == "")? $toSend["LAST_NAME"] = htmlspecialchars('<Не указано>'): $arFields["LAST_NAME"];
      CEvent::SendImmediate ("HORSE_USER_REGISTER", SITE_ID, $toSend);
   }
   return $arFields;
}
//регистрация пользователя будет записана в аналитику
AddEventHandler("main", "OnAfterUserRegister", "OnUserEmailLoginRegisterHandler");

function OnUserEmailLoginRegisterHandler(&$arFields) {

        if(CModule::IncludeModule("statistic") && intval($_SESSION["SESS_SEARCHER_ID"]) <= 0)
        {
            $event1 = "register";
            $event2 = "new_user";
            $event3 = $arFields["EMAIL"];
            CStatistic::Set_Event($event1, $event2, $event3);
        }
        return $arFields;
}

// Причина отмены в заказ из 1С и на почту юзеру

//AddEventHandler("main", "OnSaleCancelOrder", "SaleOrderHorse");  //Надо все же попробовать повесить на событие

function SaleOrderHorse ()
{

foreach (glob("/var/www/sibirix2/data/www/ohotaktiv.ru/upload/1c_exchange/Documents*.xml") as $file) { //маска искомого имени файла

	echo "xml: ".$file."<br><br>";

}

$xml = simplexml_load_file("$file"); //загрузить этот файл

foreach($xml->Документ as $item){ //ищем верхний тег

$or_id = $item->Ид; //ищем нижний тег

$or_reason = $item->ПричинаОтмены; //ищем еще глубже

echo "id: ".$or_id.'<br>';

echo "reason: ".$or_reason."<br>";

	if (!empty($or_reason)) {

	CSaleOrder::CancelOrder($or_id, "Y", $or_reason);

}

}

}

AddEventHandler('main', 'OnEndBufferContent', 'controller404', 1001);
function _Check404Error(){
  if(defined('ERROR_404') && ERROR_404=='Y' || CHTTP::GetLastStatus() == "404 Not Found"){
     GLOBAL $APPLICATION;
     $APPLICATION->RestartBuffer();
     require $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/header.php';
     require $_SERVER['DOCUMENT_ROOT'].'/404.php';
     require $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/footer.php';
  }
}

//controller for down
AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", Array("MyClass", "OnBeforeIBlockElementUpdateHandler"));

class MyClass
{
    function OnBeforeIBlockElementUpdateHandler(&$arFields)
    {
        global $DB, $USER;
		$username = $USER->GetID();
		$text = $arFields['DETAIL_TEXT'];
		$prev_pic = $row['PREVIEW_PICTURE'];	
		$detail_pics = $row['DETAIL_PICTURE'];	
		
		$prod_id = $arFields['ID'];
		$url = $arFields['ID'];		
		
		$before = $DB->Query("SELECT DETAIL_PICTURE, PREVIEW_PICTURE, DETAIL_TEXT,TIMESTAMP_X FROM b_iblock_element WHERE ID = '{$prod_id}'");
		
		while ($row = $before->Fetch())
{
		$rrr = $row['DETAIL_TEXT'];	
		
		$new_prev_pic = $row['PREVIEW_PICTURE'];	
		$new_detail_pics = $row['DETAIL_PICTURE'];	
		
		$rrr = preg_replace('/\s?<style[^>]*?>.*?<\/style>\s?/si', ' ', $rrr);
		$rrr = preg_replace('/\s?<script[^>]*?>.*?<\/script>\s?/si', ' ', $rrr);
		$rrr = preg_replace('/\s?<span[^>]*?>.*?<\/span>\s?/si', ' ', $rrr);
		
		$text = preg_replace('/\s?<style[^>]*?>.*?<\/style>\s?/si', ' ', $text);
		$text = preg_replace('/\s?<script[^>]*?>.*?<\/script>\s?/si', ' ', $text);
		$text = preg_replace('/\s?<span[^>]*?>.*?<\/span>\s?/si', ' ', $text);
		
		$date_edit = $row['TIMESTAMP_X'];
		
		if ($username = 13692) {
			$results = $DB->Query("INSERT INTO Reports VALUES ('', '{$username}', '{$url}', '{$date_edit}', '{$detail_pics}','{$new_detail_pics}')");
		} else {		
		$results = $DB->Query("INSERT INTO Reports VALUES ('', '{$username}', '{$url}', '{$date_edit}', '{$rrr}','{$text}')");
		}
}			
	}
}