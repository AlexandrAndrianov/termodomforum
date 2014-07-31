<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?><?/*$APPLICATION->IncludeComponent(
	"bitrix:forum.user.post",
	"",
	array(
		"UID" =>  $arResult["UID"],
		"mode" =>  $arResult["mode"],
		
		"URL_TEMPLATES_LIST" =>  $arResult["URL_TEMPLATES_LIST"],
		"URL_TEMPLATES_READ" => $arResult["URL_TEMPLATES_READ"],
		"URL_TEMPLATES_MESSAGE" => $arResult["URL_TEMPLATES_MESSAGE"],
		"URL_TEMPLATES_USER_LIST" =>  $arResult["URL_TEMPLATES_USER_LIST"],
		"URL_TEMPLATES_PROFILE_VIEW" => ($arParams["SEO_USER"] == "TEXT" ? "" : $arResult["URL_TEMPLATES_PROFILE_VIEW"]),
		"URL_TEMPLATES_USER_POST" =>  $arResult["URL_TEMPLATES_USER_POST"],
		"URL_TEMPLATES_PM_EDIT" => $arResult["URL_TEMPLATES_PM_EDIT"],
		"URL_TEMPLATES_MESSAGE_SEND" => $arResult["URL_TEMPLATES_MESSAGE_SEND"],

		"USER_FIELDS" => $arParams["USER_FIELDS"],
		"MESSAGES_PER_PAGE" => $arParams["MESSAGES_PER_PAGE"],
		"FID_RANGE" => $arParams["FID"],
		"DATE_FORMAT" =>  $arParams["DATE_FORMAT"],
		"NAME_TEMPLATE"	=> $arParams["NAME_TEMPLATE"],
		"DATE_TIME_FORMAT" =>  $arParams["DATE_TIME_FORMAT"],
		"PAGE_NAVIGATION_TEMPLATE" =>  $arParams["PAGE_NAVIGATION_TEMPLATE"],
		"PATH_TO_SMILE" => $arParams["PATH_TO_SMILE"],
		"PATH_TO_ICON" => $arParams["PATH_TO_ICON"],
		"WORD_LENGTH" => $arParams["WORD_LENGTH"],
		"IMAGE_SIZE" => $arParams["IMAGE_SIZE"],
		"ATTACH_MODE" => $arParams["ATTACH_MODE"],
		"ATTACH_SIZE" => $arParams["ATTACH_SIZE"],
		"SET_NAVIGATION" => $arParams["SET_NAVIGATION"],
		"SET_TITLE" => $arParams["SET_TITLE"],
		"SEND_MAIL" => $arParams["SEND_MAIL"],
		"SEND_ICQ" => $arParams["SEND_ICQ"],

		"SEO_USER" => $arParams["SEO_USER"],

		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
	),
	$component 
);
*/?>

	<?
		global $USER;
		$arAutoriz ="";
		if ($USER->IsAuthorized())
		{
			$arAutoriz = "Q" ;
		}	
	?>

<?//**************Andrianov A.M. 31.07.2014******************************
if(!function_exists ('inclination'))
{
		/*Функция склонения*/
	function inclination($time, $arr=array("отзыв","отзыва","отзывов")) 
	{
		$timex = substr($time, -1);
		if ($time >= 10 && $time <= 20)
			return $arr[2];
		elseif ($timex == 1)
			return $arr[0];
		elseif ($timex > 1 && $timex < 5)
			return $arr[1];
		else
			return $arr[2];
	}
}

if(!function_exists ('EditData'))
{
		function EditData ($DATA) // конвертирует формат даты с 04.11.2008 в 04 Ноября, 2008
		{
				$MES = array( 
				"01" => "Января", 
				"02" => "Февраля", 
				"03" => "Марта", 
				"04" => "Апреля", 
				"05" => "Мая", 
				"06" => "Июня", 
				"07" => "Июля", 
				"08" => "Августа", 
				"09" => "Сентября", 
				"10" => "Октября", 
				"11" => "Ноября", 
				"12" => "Декабря"
				);
				$arData = explode(".", $DATA); 
				$d = ($arData[0] < 10) ? substr($arData[0], 1) : $arData[0];

				$newData = $d." ".$MES[$arData[1]]." ".$arData[2]; 
				return $newData;
		}
}	//**************END_Andrianov A.M. 31.07.2014******************************		
?>
	<?$idUSER = $arResult['UID']?>

	<div class="grid-8">
		<div class="block">
			
			<!----------Andrianov A.M. 31.07.2014----------------->
			<h2>Мои сообщения на форуме</h2>

				
				<div class="forum-block-posts">
					
					<?
						$db_res = CForumMessage::GetList(array("TOPIC_ID "=>"DESC"), array("AUTHOR_ID"=>$idUSER));
						$count = 0;
						while ($ar_res = $db_res->Fetch())
						{
							$arPst [$count]['id'] = $ar_res['TOPIC_ID'];
							$arPst [$count]['date'] = $ar_res['POST_DATE'];
							$arPst [$count]['mess'] = $ar_res['POST_MESSAGE_HTML'] ;
							$arPst [$count]['idmess'] = $ar_res['ID'] ;
							++$count;
						}
						sort($arPst);
						
						$flag = true;
						foreach($arPst as $key=>$value)
						{
							if($flag):?>
							<div class="forum-post">	
								<div class="post-theme">
									<?$arTopic = CForumTopic::GetByID($value['id']);
										$arForum = CForumNew::GetByID($arTopic['FORUM_ID']);
										$arGroup = CForumGroup::GetByIDEx($arForum['FORUM_GROUP_ID']);
									?>
									<em class="forum-cat-prelink">
										<?if($arGroup):?>
											<a href=""><?=$arGroup['NAME']?></a>
											>	
										<?endif?>	
											<a href=""><?=$arForum['NAME']?></a>
									</em>
									<h4>
										<?
											$opros ='';
											$db_opros = CForumMessage::GetList(array("ID"=>"ASC"), array("TOPIC_ID"=>$arTopic['ID'], 'PARAM1'=>'VT'));
											while ($ar_opros = $db_opros->Fetch())
											{
											  $opros = $ar_opros['PARAM1'];
											}

										?>
									
									
										<?if (intval($arTopic["SORT"]) != 150 && $arTopic["STATE"]!="Y"):
												?>
											<?if ($arTopic["STATE"] != "L"):
												?><i class="fa fa-lock"></i>
											<?endif?>
												<strong>Важно, <?
											if ($arTopic["STATE"] != "L"):
												?>Закрыто:&nbsp;</strong><?
											else:
												?>Перемещенно:&nbsp;</strong><?
											endif;
										elseif ($arTopic["TopicStatus"] == "MOVED" || $arTopic["STATE"]=="L"):
												?><strong>Перемещенно:&nbsp;</strong><?
										elseif (($opros || $arTopic["STATE"]=="L") && $arTopic["STATE"]=="Y"):
												?><strong>Опрос:&nbsp;</strong><?
										elseif (intVal($arTopic["SORT"]) != 150):
												?><strong>Важно:&nbsp;</strong><?
										elseif (($arTopic["STATE"]!="Y") && ($arTopic["STATE"]!="L")):
												?><i class="fa fa-lock"></i>
												<strong>Закрыто:&nbsp;</strong><?
										elseif ($opros && ($arTopic["STATE"]=="N")):
												?><i class="fa fa-lock"></i>
												<strong>Закрыто:&nbsp;</strong><?
										endif;
												?>
												
												<span class="forum-item-title"><?
										if (false && strLen($arTopic["IMAGE"]) > 0):
												?><img src="<?=$arParams["PATH_TO_ICON"].$arTopic["IMAGE"];?>" alt="<?=$arTopic["IMAGE_DESCR"];?>" border="0" width="15" height="15"/><?
										endif;
												?><a href="<?=$arTopic["URL"]["TOPIC"]?>" title="Тема начата <?=$arTopic["START_DATE"]?>"><?=$arTopic["TITLE"]?></a><?
										if ($arTopic["TopicStatus"] == "NEW" && strLen($arParams["~TMPLT_SHOW_ADDITIONAL_MARKER"]) > 0):
												?><noindex><a href="<?=$arTopic["URL"]["MESSAGE_UNREAD"]?>" rel="nofollow" class="forum-new-message-marker"><?=$arParams["~TMPLT_SHOW_ADDITIONAL_MARKER"]?></a></noindex><?
										endif;
												?></span><?
										if ($arTopic["PAGES_COUNT"] > 1):
												?> <!--<span class="forum-item-pages">(<?
											$iCountPages = intVal($arTopic["PAGES_COUNT"] > 5 ? 3 : $arTopic["PAGES_COUNT"]);
											for ($ii = 1; $ii <= $iCountPages; $ii++):
												?><noindex><a rel="nofollow" href="<?=ForumAddPageParams($arTopic["URL"]["~TOPIC"], ($ii > 1 ? array("PAGEN_".$arParams["PAGEN"] => $ii) : array()))?>"><?
													?><?=$ii?></a></noindex><?=($ii < $iCountPages ? ",&nbsp;" : "")?><?
											endfor;
											if ($iCountPages < $arTopic["PAGES_COUNT"]):
												?>&nbsp;...&nbsp;<noindex><a rel="nofollow" href="<?=ForumAddPageParams($arTopic["URL"]["~TOPIC"], 
													array("PAGEN_".$arParams["PAGEN"] => $arTopic["PAGES_COUNT"]))?>"><?=$arTopic["PAGES_COUNT"]?></a></noindex><?
											endif;
												?>)</span>--><?
										endif;
											?>
									</h4>
								</div>
							<?endif;?>
								<div class="post-theme">
									<?$flag = false;?>
									<i>
										<a title="Ссылка на это сообщение" 
										onclick="prompt(this.title + ' [' + this.innerHTML + ']',
														(location.protocol + '//' + location.host + this.getAttribute('href')));
														return false;"href="/Да буде тута ссылка">#<?=$value['idmess']?></a>
									</i>
									<span class="post-date"><?=EditData($value['date'])?></span>
								</div>
								<div class="forum-post-content">
									<p><?echo $value['mess'];?></p>
								</div>
							<?if($value['id'] !=$arPst[$key+1]['id']):?>
							</div><!--forum-post-->
							<?
								$flag = true;
							endif;
						}		
					?>						
	
				</div>
			
			<!----------END_Andrianov A.M. 31.07.2014----------------->
		</div><!--block-->
	</div>	
	<div class="grid-4">
		<div class="block">
			<h2>Личный кабинет<h2>
		</div>
	</div>
