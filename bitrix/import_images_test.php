<?
use Bitrix\Main\Web\HttpClient;
define('NO_AGENT_CHECK', true);             //отключает выполнение всех агентов
define('NO_AGENT_STATISTIC', 'Y');          //запрету некоторых действий модуля "Статистика"
define("NO_KEEP_STATISTIC", true);          //запрет сбора статистики
define("NOT_CHECK_PERMISSIONS",true);       //отключение проверки прав на доступ к файлам и каталогам

$arServer = Array();
$arServer['REMOTE_ADDR'] = '';
$arServer['HTTP_HOST'] = 'derevyanko2.smrtp.ru';
$arServer['SERVER_NAME'] = $arServer['HTTP_HOST'];
$arServer['DOCUMENT_ROOT'] = __DIR__ . '/../';//возможно понадобится поправить
$_SERVER = (is_array($_SERVER) ? $_SERVER : Array());
$_SERVER = array_merge($_SERVER, $arServer);

require($_SERVER["DOCUMENT_ROOT"] . "bitrix/modules/main/include/prolog_before.php");

while (ob_get_level())
{
	ob_end_clean();
}

global $DB;
CModule::IncludeModule("iblock");
$oElement = new \CIBlockElement();

$tempImagesDir = $_SERVER['DOCUMENT_ROOT'] . 'upload/temp_images/';
\Bitrix\Main\IO\Directory::createDirectory($tempImagesDir);

$rsProducts = CIBlockElement::GetList(
	array("ID" => "DESC"),
	array(
		"IBLOCK_ID" => 6,
		array(
			'LOGIC' => 'OR',
			"DETAIL_PICTURE" => false,
			"PREVIEW_PICTURE" => false,
		)
	),
	false,
	false,
	array(
        'ID',
        'PROPERTY_CML2_ARTICLE',
		'IBLOCK_ID',
		'DETAIL_PICTURE',
		'PREVIEW_PICTURE',
	)
);

$arQueries = array();
while ($arProduct = $rsProducts->fetch()) {
	if ((!$arProduct['DETAIL_PICTURE'] || !$arProduct['PREVIEW_PICTURE'])) {
		$arImages = findImagesByArticle($arProduct['PROPERTY_CML2_ARTICLE_VALUE']);//Поменять на используемую переменную //Получаем массив адресов изображений
		
		$mainImage = trim(array_shift($arImages));//забирает один элемент(первый)

		if ($mainImage) {
			$imagePath = '';
			$fileName = end(explode('/', $mainImage));

			if ($fileName) {
				$imagePath = $tempImagesDir . $fileName;

				file_put_contents($imagePath, file_get_contents($mainImage));

				if (filesize($imagePath) == 0) {
					unlink($imagePath);
					$ch = curl_init($mainImage);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
					$response = curl_exec($ch);
					curl_close($ch);
					file_put_contents(
						$imagePath,
						$response
					);
				}

				if ($imagePath && file_exists($imagePath) && filesize($imagePath) > 0) {
					$fields = array();

					if (!$arProduct['PREVIEW_PICTURE']) {
						$fields['PREVIEW_PICTURE'] = \CFile::MakeFileArray($imagePath);
					}

					if (!$arProduct['DETAIL_PICTURE']) {
						$fields['DETAIL_PICTURE'] = \CFile::MakeFileArray($imagePath);
					}

					if ($fields) {
						$oElement->Update($arProduct['ID'], $fields);
					}
				}
				
				unlink($imagePath);
			}
		}

		if ($arImages) {
			$imagesPaths = array();
			$additionalImagesProp = array();

			foreach ($arImages as $addImage) {
				$addImage = trim($addImage);
				if ($addImage) {
					$imagePath = '';
					$fileName = end(explode('/', $addImage));
					if ($fileName) {
						$imagePath = $tempImagesDir . $fileName;
						file_put_contents($imagePath, file_get_contents($addImage));

						if (filesize($imagePath) == 0) {
							unlink($imagePath);
							$ch = curl_init($addImage);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
							$response = curl_exec($ch);
							curl_close($ch);
							file_put_contents(
								$imagePath,
								$response
							);
						}

						if ($imagePath && file_exists($imagePath) && filesize($imagePath) > 0) {
							$imagesPaths[] = $imagePath;
							$additionalImagesProp[] = \CFile::MakeFileArray($imagePath);;
						}
					}
				}
			}

			if ($additionalImagesProp) {
				\CIBlockElement::SetPropertyValuesEx($arProduct['ID'], false, array('MORE_PHOTO' => $additionalImagesProp));
			}

			foreach ($imagesPaths as $imagePath) {
				unlink($imagePath);
			}
		}
	}
	//break;
}



//Функция собирает пути к изображениям в массив
function findImagesByArticle($article) {
	$extensionsList = ['.jpg', '.jpeg', '.JPG', '.JPEG', '.png' , '.PNG'];
	$dir = 'https://russian-nail-shop.ru/admin/pictures/';

	$articles[] = $article . 'b';
	for ($i= 1; $i <= 10; $i++) {
		$articles[] = $article . 'b-' . $i;
	}

	$arImages = [];
	foreach ($articles as $article) {
		foreach ($extensionsList as $extension) {
			$filePath = $dir . $article . $extension;
			
			$httpClient = new HttpClient();
			$httpClient->head("https://snipp.ru/uploads/view/350x0/5863b9fd0b514b7b94adbbb4ccaae121.png");
			dump($httpClient);
			$newLocation = $httpClient->getHeaders();
			if ((($httpClient->getStatus() == 301 || $httpClient->getStatus() == 302) && $newLocation)) {
				$arImages[] = $newLocation;
			} elseif ($httpClient->getStatus() == 200) {
				$arImages[] = $filePath;
			}
		}
	}

	return $arImages;
}