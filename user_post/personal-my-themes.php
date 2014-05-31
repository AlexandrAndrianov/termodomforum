<? /*define("NEED_AUTH", true);*/?><?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Профиль");
?>
<?
    global $USER;
    $idUSER = (isset($_GET["id"]))?intval($_GET["id"]):$USER->GetId();
    $idCurUSER=$USER->GetID();
    
    $isIm = ($idUSER==$idCurUSER);
    
    $rsUser = CUser::GetByID($idUSER);
    if (!$arUser = $rsUser->Fetch()) {
        LocalRedirect("/404.php");
    } elseif ($arUser['ACTIVE']=="N") {
        LocalRedirect("/404.php");
    }
//**************Andrianov A.M. 16.07.2014******************************
	$arAutoriz ="";
		if ($USER->IsAuthorized())
		{
			$arAutoriz = "Q" ;
		}	
		
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

//**************END_Andrianov A.M. 16.07.2014******************************	
    
$URLimg = "/bitrix/components/avekom/user.photo/templates/.default/images/man.JPG";
if ($arUser['PERSONAL_PHOTO']) {
    $URLimg = CFile::GetPath($arUser['PERSONAL_PHOTO']);
} else if ($arUser['PERSONAL_GENDER'] == "F") {
    $URLimg = "/bitrix/components/avekom/user.photo/templates/.default/images/girl.JPG";
}
//if ($USER->IsAdmin()) {print_r($arUser);}
?>
<div id="content-surround">
        <div class="content"><!--content-->
        	<div class="container">

            	<div class="grid-8"><!-- rb -->
                    <div class="block"><!-- block-->
							<!----------Andrianov A.M. 16.07.2014----------------->
							<h2>Мои темы</h2>
							<div class="forum-block raised-corners">
								<div class="forum-block-header">
									<h3>Список тем</h3>
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
																		
																		/***UrlPath************************/
																		$pathForum = "/communication/forum/";
																		$pathTopic =  "forum#FID#/";
																		
																		$arTopicUrl = array('FID' => $arForum['ID']);
																		$pageTopic = CComponentEngine::MakePathFromTemplate($pathTopic, $arTopicUrl);
																		
																		/*****EndUrlPatch******************/
																	?>
																	<?if($arGroup):?>
																		<a href="<?=$pathForum.'group'.$arGroup['ID']?>"><?=$arGroup['NAME']?></a>
																		>	
																	<?endif?>	
																		<a href="<?=$pathForum.$pageTopic?>"><?=$arForum['NAME']?></a>
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
																			?><a href="<?=$pathForum.$pageTopic.$arTopic['TITLE_SEO']?>" title="Тема начата <?=$res["START_DATE"]?>"><?=$res["TITLE"]?></a><?
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
																					<?$answer = inclination($res['POSTS'] + 1, array("ответ","ответа","ответов"));?>
																					<strong><?=$res['POSTS'] + 1?> <?=$answer?>, </strong>
																					последний от
																				
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
							<!----------END_Andrianov A.M. 16.07.2014----------------->
							
                    </div><!-- /block-->
                </div><!-- /rb -->
                    <div class="grid-4"><!--lb-->
                        <div class="block">
                            <h2>Личный кабинет</h2>
                        <?
                            $APPLICATION->IncludeComponent("bitrix:menu", "lich_kab_menu", array(
                                "ROOT_MENU_TYPE" => "left",
                                "MENU_CACHE_TYPE" => "N",
                                "MENU_CACHE_TIME" => "3600",
                                "MENU_CACHE_USE_GROUPS" => "Y",
                                "MENU_CACHE_GET_VARS" => array(
                                ),
                                "MAX_LEVEL" => "1",
                                "CHILD_MENU_TYPE" => "left",
                                "USE_EXT" => "N",
                                "DELAY" => "N",
                                "ALLOW_MULTI_SELECT" => "N"
                                    ), false
                            );
                            ?>
                        </div>
                    </div><!-- /lb -->
                    <div class="clear"></div>
            </div>
        </div><!--/content-->
        </div>
<?
?>
     <div id="modalform" class="qa-form hide"> 
            <div class="header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3>Личное сообщение</h3>
            </div>
            <div class="form-text">
                <form>
                    <div class="qa-form-field-group">
                        <div class="label">
                            <label>Текст: </label>
                        </div>
                        <div class="field">
                            <textarea></textarea>
                        </div>
                    </div>
                    <div class="qa-form-buttons">
                        <button data-dismiss="modal" class="btn gray float-right small-margin-left">Закрыть</button>
                        <button class="btn green float-right">Отправить</button>
                        <div class="clear"></div>
                    </div>

                </form>
            </div>
        </div> 
  <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>