<?
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

?>
