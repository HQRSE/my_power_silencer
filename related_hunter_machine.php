<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/classes/general/xml.php');
$APPLICATION->SetTitle("related hunter");
?>

<div class="container related_hunter">

<?
$strQueryText = "/var/www/sibirix2/data/www/ohotaktiv.ru/obmen_files/folders/Related_folders_2.xml";
$objXML = new CDataXML();
    $objXML->Load($strQueryText);
    $arData = $objXML->GetArray();
	
	/* start formatter level 1 */
    $arResult = array();
    foreach ($arData['items']['#']['item'] as $arValue) {
        $ar = array();
        foreach ($arValue['#'] as $sKey => $sVal) {
            $ar[$sKey] = $sVal[0]['#'];
        }
        $arResult[] = $ar;
    }
	/* start formatter level 2 */
    $j = 0;
    $sk = array();

    foreach ($arResult as $qwe => $guid) {

        $id[$j]['guid_item'] = $guid['guid_item'];
		//echo "item_guid: " . $id[$j]['guid_item'] . "<br>";

        $i = 0;
        while (count($guid['items_folders']['guid_folder']) > $i) {
            $id[$j]['items_folders'][$i] = $guid['items_folders']['guid_folder'][$i]['#'];
			//echo "item_folder_guid-".$i.": " . $id[$j]['items_folders'][$i] . "<br>";
        $i++;
		}

		$t = 0;
        while (count($guid['related_folders']['guid_folder']) > $t) {
            $id[$j]['related_folders'][$t] = $guid['related_folders']['guid_folder'][$t]['#'];
			//echo "related_folder_guid-".$t.": " . $id[$j]['related_folders'][$t] . "<br>";
		$t++;
        }
    $j++;
    }

	/* start formatter level 3 */

	$k = 0;
	$extraction = array();
		foreach ($id as $bunny) {
		$circle = 0;
			/* check it */
			while (count($bunny['related_folders']) > $circle && !empty($bunny['related_folders'][$circle])) {
				foreach ($id as $dog) {
					if (in_array($bunny['related_folders'][$circle], $dog['items_folders'])) {
					//echo "for ".$bunny['guid_item']." related ".$dog['guid_item']."<br>";
						/* ** */
						/* *** */
						$raw = $dog['guid_item'];
						$saw = $bunny['guid_item'];
						$results = $DB->Query("SELECT ID FROM b_iblock_element WHERE XML_ID='$raw' LIMIT 1");
						while ($row = $results->Fetch())
						{
						$dog = $row['ID'];
						}
						$results = $DB->Query("SELECT ID FROM b_iblock_element WHERE XML_ID='$saw' LIMIT 1");
						while ($row = $results->Fetch())
						{
						$bunny = $row['ID'];
						}
						/* *** */
						/* ** */
 					$extraction[$k] = array('guid' => $bunny, 'related' => $dog);
					$k++;
					}
				}
		$circle++;
			}
		}

	/* * Show Me The Loot * */

	function ShowMeTheLoot($extraction) {
		$loot = array();
		foreach ($extraction as $l) {
		$loot[$l['guid']][] = $l['related'];
  		}
	 return $loot;
	}

echo "<pre>";
print_r(ShowMeTheLoot($extraction));
echo "</pre>";

?>

</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
