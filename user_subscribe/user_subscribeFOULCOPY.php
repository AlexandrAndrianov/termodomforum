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

<?
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
}		
?>
	<?$idUSER = $arResult['UID']?>

	<div class="grid-8">
		<div class="block">
			
	<form method="get">	
			<h2>Подписки</h2>
			<div class="forum-block raised-corners">
				<div class="forum-block-header">
					<h3>Список тем</h3>
				</div>
				<div class="forum-block-posts">
					<ul class="forum-themes-wo-counter">
						<?
							$db_subscr = CForumSubscribe::GetList(array("START_DATE" => "ASC"), 
													array("USER_ID" => $idUSER ));
							while($res_sub = $db_subscr->Fetch())
							{
								
								$arTopic = array();
								$arTopic[] = $res_sub['TOPIC_ID'];
								
								$res = CForumTopic::GetByID($res_sub['TOPIC_ID']);

								// get last_visit dates
								$arVisit = CForumUser::GetUserTopicVisits($res_sub['FORUM_ID'], $arTopic);
								foreach($arVisit as $visit)
								{
									$lastvisit = $visit;
								}
								
								
								$res["TopicStatus"] = "OLD";
								if ($res["APPROVED"] != "Y")
								{
									$res["TopicStatus"] = "NA";
								}
								elseif ($res["STATE"] == "L")
								{
									$res["TopicStatus"] = "MOVED";
								}
								
								if (
										($res["TopicStatus"] == "OLD") &&
										NewMessageTopic(
											$res["FORUM_ID"],
											$res["ID"],
											($arAutoriz < "Q" ? $res["LAST_POST_DATE"] : $res["ABS_LAST_POST_DATE"]),
											$lastvisit
										)
									)
								{
									$res["TopicStatus"] = "NEW";
								}
								
							  ?>
		
							  <?
								$opros ='';
								$db_opros = CForumMessage::GetList(array("ID"=>"ASC"), array("TOPIC_ID"=>$res['ID'], 'PARAM1'=>'VT'));
								while ($ar_opros = $db_opros->Fetch())
								{
								  $opros = $ar_opros['PARAM1'];
								}

							?>
							  <li class="<?=($res['TopicStatus'] == 'NEW'? 'has-message' : ' ')?>">	
							  
										<div class="control-box">
											<input name ="substr[]" value="<?=$res_sub['ID']?>" class="control-box-checkbox" type="checkbox">
										</div>
										
										<div class="forum-description">
											<div class="forum-view-table">
												<div class="heading">
													<em class="forum-cat-prelink">
													
													<?$arForum = CForumNew::GetByID($res['FORUM_ID']);

														$arGroup = CForumGroup::GetByIDEx($arForum['FORUM_GROUP_ID']);
														
														/***UrlPath************************/
														$pathForum = "/communication/forum/";
														$pathTopic =  "forum#FID#/";
														$pathMess = "messages/forum#FID#/message#MID#/#TITLE_SEO#";
														
														$arTopicUrl = array('FID' => $arForum['ID']);
														$arMessUrl = array('FID' => $arForum['ID'], 'MID' => $value['idmess'],
																			'TITLE_SEO' => $arTopic['TITLE_SEO']);
									
														$pageTopic = CComponentEngine::MakePathFromTemplate($pathTopic, $arTopicUrl);
														$pageMess = CComponentEngine::MakePathFromTemplate($pathMess, $arMessUrl);
										
										
			
										/*****EndUrlPatch******************/
	
													?>
													<?if($arGroup):?>
														<a href="<?=$pathForum.'group'.$arGroup['ID']?>"><?=$arGroup['NAME']?></a>
														>	
													<?endif?>	
														<a href=""><?=$arForum['NAME']?></a>
													</em>
													<h4>
														<?if (intval($res["SORT"]) != 150 && $res["STATE"]!="Y"):
															?>
														<?if ($res["STATE"] != "L"):
															?><i class="fa fa-lock"></i>
														<?endif?>
															<strong>Важно, <?
														if ($res["STATE"] != "L"):
															?>Закрыто:&nbsp;</strong><?
														else:
															?>Перемещенно:&nbsp;</strong><?
														endif;
													elseif ($res["TopicStatus"] == "MOVED" || $res["STATE"]=="L"):
															?><strong>Перемещенно:&nbsp;</strong><?
													elseif (($opros || $res["STATE"]=="L") && $res["STATE"]=="Y"):
															?><strong>Опрос:&nbsp;</strong><?
													elseif (intVal($res["SORT"]) != 150):
															?><strong>Важно:&nbsp;</strong><?
													elseif (($res["STATE"]!="Y") && ($res["STATE"]!="L")):
															?><i class="fa fa-lock"></i>
															<strong>Закрыто:&nbsp;</strong><?
													elseif ($opros && ($res["STATE"]=="N")):
															?><i class="fa fa-lock"></i>
															<strong>Закрыто:&nbsp;</strong><?
													endif;
															?>
															
															<span class="forum-item-title"><?
													if (false && strLen($res["IMAGE"]) > 0):
															?><img src="<?=$arParams["PATH_TO_ICON"].$res["IMAGE"];?>" alt="<?=$res["IMAGE_DESCR"];?>" border="0" width="15" height="15"/><?
													endif;
															?><a href="<?=$pathForum.$pageTopic.$res['TITLE_SEO']?>" title="Тема начата <?=$res["START_DATE"]?>"><?=$res["TITLE"]?></a><?
													if ($res["TopicStatus"] == "NEW" && strLen($arParams["~TMPLT_SHOW_ADDITIONAL_MARKER"]) > 0):
															?><noindex><a href="<?=$res["URL"]["MESSAGE_UNREAD"]?>" rel="nofollow" class="forum-new-message-marker"><?=$arParams["~TMPLT_SHOW_ADDITIONAL_MARKER"]?></a></noindex><?
													endif;
															?></span><?
													if ($res["PAGES_COUNT"] > 1):
															?> <!--<span class="forum-item-pages">(<?
														$iCountPages = intVal($res["PAGES_COUNT"] > 5 ? 3 : $res["PAGES_COUNT"]);
														for ($ii = 1; $ii <= $iCountPages; $ii++):
															?><noindex><a rel="nofollow" href="<?=ForumAddPageParams($res["URL"]["~TOPIC"], ($ii > 1 ? array("PAGEN_".$arParams["PAGEN"] => $ii) : array()))?>"><?
																?><?=$ii?></a></noindex><?=($ii < $iCountPages ? ",&nbsp;" : "")?><?
														endfor;
														if ($iCountPages < $res["PAGES_COUNT"]):
															?>&nbsp;...&nbsp;<noindex><a rel="nofollow" href="<?=ForumAddPageParams($res["URL"]["~TOPIC"], 
																array("PAGEN_".$arParams["PAGEN"] => $res["PAGES_COUNT"]))?>"><?=$res["PAGES_COUNT"]?></a></noindex><?
														endif;
															?>)</span>--><?
													endif;
														?>
													</h4>
													<?						if ($res["LAST_MESSAGE_ID"] > 0):
													?>
															<p>
															
																	<?$crtDataPst = EditData($res["START_DATE"]);?>
																	Автор: <noindex><a  href="<?=$res["USER_START_NAME"]?>"><?=$res["USER_START_NAME"]?></a> &raquo; <?=$crtDataPst ?></noindex>
																
															</p>
													<?
															else:
													?>
															&nbsp;
													<?
															endif;
													?>
												</div>
											</div>
										</div>
							  </li>
							  <?
							}
							
						
						?>
															
					</ul>
					<div class="forum-admin-controls clear">
						<a href="javascript: void(0)" onclick ="selall()">Выделить все</a>
						<select class="inputbox" name="select">
							<option disabled="disabled" selected="selected">Управление темами</option>
							<option value="tru">Отписаться</option>
						</select>
						<input class="btn gray" type="submit" value="OK">
					</div>
				</div>
			</div>
</form>			
			
		</div>
	</div>	
	<div class="grid-4">
		<div class="block">
			<h2>Личный кабинет<h2>
		</div>
	</div>


	<script type="text/javascript">
	
		
		$('.control-box-checkbox').bind('change',
				function(e){
					var li = e.target.parentNode.parentNode;
					if($(li).attr("class") === 'checked' )
					{
						$(li).attr("class", ' ');
					}
					else $(li).attr("class", 'checked');
				});
	
	function selall(){
		var check = $('input[type=checkbox]');
		var count = check.length
		for(var i =0; i < count; i++)
		{
			if(check[i].checked)
			{
				$(check[i]).removeAttr('checked');
				$(check[i]).change();
			}
			else
			{
				$(check[i]).attr('checked', 'checked');
				$(check[i]).change();
			}
		}
	}
	
	</script>
	
	<? 
		if(!strcmp($_GET['select'] ,'tru'))
		{
			$len = count($_GET['substr']);
			if($len)
			{
				for(; $len >=0; $len -= 1)
				{
					CForumSubscribe::Delete($_GET['substr'][$len]);
				}
			}
			
			header("Location:".$pathForum."user/".$idUSER."/post/all/");
			
		}
	?>
