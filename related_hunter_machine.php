<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/classes/general/xml.php');
$APPLICATION->SetTitle("related hunter");
?>

<div class="container related_hunter">

<?
$glob = glob("/var/www/sibirix2/data/www/ohotaktiv.ru/obmen_files/folders/Related_goods_*.xml");

foreach ($glob as $file) { 
	//echo count($glob)."<br>";
}

echo "file_count: ".count($glob)."<br>";

$file_count = 0;

while (count($glob) > $file_count) {

$q_file = $glob[$file_count];

/* start bunny */
$breed_of_bunny = $q_file;

$carrot = new CDataXML();
    $carrot->Load($breed_of_bunny);
    $hide_in_the_woods = $carrot->GetArray();

	/* start formatter level 1 */
    $bounce = array();
    foreach ($hide_in_the_woods['items']['#']['item'] as $saw) {
        $ar = array();
        foreach ($saw['#'] as $iHide => $iEat) {
            $ar[$iHide] = $iEat[0]['#'];
        }
        $bounce[] = $ar;
    }

	/* start formatter level 2 */
    $j = 0;

    foreach ($bounce as $jump => $hop) {

        $asylum_for_bunny[$j]['guid_item'] = $hop['guid_item'];
		
      	$t = 0;
        while (count($hop['related_folders']['guid_folder']) > $t) {
            $asylum_for_bunny[$j]['related_folders'][$t] = $hop['related_folders']['guid_folder'][$t]['#'];
		$t++;
        }
    $j++;
    }

	/* end formatter level 2 */

/* start dog */
$breed_of_dog = "/var/www/sibirix2/data/www/ohotaktiv.ru/obmen_files/folders/Related_folders_00.xml";
$bone = new CDataXML();
    $bone->Load($breed_of_dog);
    $seek = $bone->GetArray();

echo "<pre>";
//print_r($seek); 
echo "</pre>";
	/* start formatter level 1 */
    $sit = array();
    foreach ($seek['related_folders']['#']['related_folder'] as $hide) {
        $see_nothing = array();
        foreach ($hide['#'] as $stun => $raw) {
            $see_nothing[$stun] = $raw[0]['#'];
        }
        $sit[] = $see_nothing;
    }



	/* start formatter level 2 */
    $u = 0;

    foreach ($sit as $growl => $speed) {

        $dog_loot[$u]['guid_folder'] = $speed['guid_folder'];
        $f = 0;
        while (count($speed['items']['item']) > $f) {
            $dog_loot[$u]['items'][$f] = $speed['items']['item'][$f]['#']['guid_item'][0]['#'];
        $f++;
		}
    $u++;
    }

	/* end formatter level 2 */
$extraction = array();
$k = 0;

foreach ($asylum_for_bunny as $bunny => $bun) {


	$pid = 0;

	while (count($bun['related_folders']) > $pid) {

	foreach ($dog_loot as $dog => $do) {

		if ($bun['related_folders'][$pid] == $do['guid_folder']) { // Берет охуенно! Но есть несуществующие guid товаров,
			//поэтому пустые там хуйни надо бы все + рандом каталога + рандом карточек

	$res = CIBlockElement::GetList(array(), array('IBLOCK_ID' => 10, 'ACTIVE'=>'Y', 'XML_ID' => $bun['guid_item'], 'SITE_ID' => "s1"));
        $item = $res->Fetch();

		$col = 0;
		while (count($do['items']) > $col) {

			$mad_dog = $do['items'][$col];

	$res2 = CIBlockElement::GetList(array(), array('IBLOCK_ID' => 10, 'ACTIVE'=>'Y', 'XML_ID' => $mad_dog, 'SITE_ID' => "s1"));
        $item2 = $res2->Fetch();
			if (!empty($item2)) {
			$extraction[$k]['item'] = $item['ID'];
			$extraction[$k]['related_items'][] = $item2["ID"];
			}

$col++;

		}
		
		}

	}

	$pid++;

	}

	$k++;
}

while ($fruit_name = current($extraction)) {


$i_id = $extraction[key($extraction)]['item'];
	echo "<strong>i:</strong> ".$i_id.'<br />';
$o = 0;
$r_id = '';
	while (count($extraction[key($extraction)]['related_items']) > $o) {
		if ((count($extraction[key($extraction)]['related_items']) - 1) <> $o) {
$r_id .= $extraction[key($extraction)]['related_items'][$o].", ";
		} else {
$r_id .= $extraction[key($extraction)]['related_items'][$o];
		}
/* *** */
$ELEMENT_ID = $i_id;  // код элемента
$PROPERTY_CODE = "related_sections";  // код свойства
$PROPERTY_VALUE = $r_id;  // значение свойства
// Установим новое значение для данного свойства данного элемента
CIBlockElement::SetPropertyValuesEx($ELEMENT_ID, false, array($PROPERTY_CODE => $PROPERTY_VALUE));
/* **** */
$o++;
	}
	echo "<strong>rid:</strong> ".$r_id."<br>";

    next($extraction);
}

/* ************************* */ 
echo "all: ".count($extraction)."<br>";

/* ************************* */ 

$file_count++;

}

/* ************************* */ 

echo "<pre>";
print_r($extraction); 
echo "</pre>";

?>

</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
