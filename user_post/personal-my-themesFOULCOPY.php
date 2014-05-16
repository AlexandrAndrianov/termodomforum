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
		/*������� ���������*/
	function inclination($time, $arr=array("�����","������","�������")) 
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
		function EditData ($DATA) // ������������ ������ ���� � 04.11.2008 � 04 ������, 2008
		{
				$MES = array( 
				"01" => "������", 
				"02" => "�������", 
				"03" => "�����", 
				"04" => "������", 
				"05" => "���", 
				"06" => "����", 
				"07" => "����", 
				"08" => "�������", 
				"09" => "��������", 
				"10" => "�������", 
				"11" => "������", 
				"12" => "�������"
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
			
		
			<h2>��� ����</h2>
			<div class="forum-block raised-corners">
				<div class="forum-block-header">
					<h3>������ ���</h3>
				</div>
				<div class="forum-block-posts">
					<ul class="forum-themes-wo-counter">
						<?
							$db_res = CForumTopic::GetList(array("SORT"=>"ASC", "LAST_POST_DATE"=>"DESC"), array("USER_START_ID"=>$idUSER));
							while ($res = $db_res->Fetch())
							{
								$arTopic = array();
								$arTopic[] = $res['ID'];
								// get last_visit dates
								$arVisit = CForumUser::GetUserTopicVisits($res['FORUM_ID'], $arTopic);
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
										<div class="forum-icon">
												<i class="fa fa-<?
												$title = ""; $class = "";
												if (intVal($res["SORT"]) != 150):
													$title = GetMessage("F_PINNED_TOPIC");
													if ($res["TopicStatus"] == "NEW"):
														$title .= " (".GetMessage("F_HAVE_NEW_MESS").")";
														?>info-circle<?
													else:
														?>info-circle<?
													endif;
												elseif ($res["TopicStatus"] == "MOVED"):
													$title = GetMessage("F_MOVED_TOPIC");
													?>share<?
												elseif(!empty($opros)):
													$title = ($res["TopicStatus"] == "NEW" ? $title .= GetMessage("F_HAVE_NEW_MESS") : " (".GetMessage("F_HAVE_NEW_MESS").")");
													?>bar-chart-o<?
												elseif (empty($opros)):
													$title .= (empty($title) ? GetMessage("F_HAVE_NEW_MESS") : " (".GetMessage("F_HAVE_NEW_MESS").")");
													?>file-o<?
												elseif (empty($opros)):
													$title .= (empty($title) ? GetMessage("F_NO_NEW_MESS") : "");
													?>file-o<?
												endif;
												
												?>" title="<?=$title?>"><!-- ie --></i>
									
										</div>
							  
										<div class="forum-description">
											<div class="forum-view-table">
												<div class="heading">
													<em class="forum-cat-prelink">
													
													<?$arForum = CForumNew::GetByID($res['FORUM_ID']);

														$arGroup = CForumGroup::GetByIDEx($arForum['FORUM_GROUP_ID']);
	
													?>
													<?if($arGroup):?>
														<a href=""><?=$arGroup['NAME']?></a>
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
															<strong>�����, <?
														if ($res["STATE"] != "L"):
															?>�������:&nbsp;</strong><?
														else:
															?>�����������:&nbsp;</strong><?
														endif;
													elseif ($res["TopicStatus"] == "MOVED" || $res["STATE"]=="L"):
															?><strong>�����������:&nbsp;</strong><?
													elseif (($opros || $res["STATE"]=="L") && $res["STATE"]=="Y"):
															?><strong>�����:&nbsp;</strong><?
													elseif (intVal($res["SORT"]) != 150):
															?><strong>�����:&nbsp;</strong><?
													elseif (($res["STATE"]!="Y") && ($res["STATE"]!="L")):
															?><i class="fa fa-lock"></i>
															<strong>�������:&nbsp;</strong><?
													elseif ($opros && ($res["STATE"]=="N")):
															?><i class="fa fa-lock"></i>
															<strong>�������:&nbsp;</strong><?
													endif;
															?>
															
															<span class="forum-item-title"><?
													if (false && strLen($res["IMAGE"]) > 0):
															?><img src="<?=$arParams["PATH_TO_ICON"].$res["IMAGE"];?>" alt="<?=$res["IMAGE_DESCR"];?>" border="0" width="15" height="15"/><?
													endif;
															?><a href="<?=$res["URL"]["TOPIC"]?>" title="���� ������ <?=$res["START_DATE"]?>"><?=$res["TITLE"]?></a><?
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
															
																	<i class="fa fa-comment-o"></i>
																	<?$answer = inclination($res['POSTS'] + 1, array("�����","������","�������"));?>
																	<strong><?=$res['POSTS'] + 1?> <?=$answer?>, </strong>
																	��������� ��
																
																<?$crtDataPst = EditData($res["LAST_POST_DATE"]);?>
																 <noindex><a  href="<?=$res["URL"]["LAST_POSTER"]?>"><?=$res["LAST_POSTER_NAME"]?></a> &raquo; <?=$crtDataPst ?></noindex>
																
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
				</div>
			</div>
			
			
		</div>
	</div>	
	<div class="grid-4">
		<div class="block">
			<h2>������ �������<h2>
		</div>
	</div>
