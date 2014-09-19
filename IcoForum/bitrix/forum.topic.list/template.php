<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
define("LIST_ID_THEMES", 560);
define("LIST_ID_IMG_THEMES", 561);
define("IBLOCK_ID_THEMES", 63); 
global $arPERM;
$arPERM = Array( "1", "11", "12");
?>

<?if($_GET['key'] != 'icoselect' || $_POST['key'] != 'fileico' ||
		 $_GET['key'] != 'icoselectTHEMES' || $_POST['key'] != 'fileicoTHEMES'):?>			
			<?if (!$this->__component->__parent || empty($this->__component->__parent->__name)):
				$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/components/bitrix/forum/templates/.default/style.css');
				$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/components/bitrix/forum/templates/.default/themes/blue/style.css');
				$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/components/bitrix/forum/templates/.default/styles/additional.css');
			endif;
			IncludeAJAX();
			$GLOBALS['APPLICATION']->AddHeadScript("/bitrix/js/main/utils.js");
			$GLOBALS['APPLICATION']->AddHeadScript("/bitrix/components/bitrix/forum.interface/templates/.default/script.js");
			$GLOBALS['APPLICATION']->AddHeadScript("/bitrix/components/bitrix/forum.interface/templates/popup/script.js");
			/********************************************************************
							Input params
			********************************************************************/
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
															
			/***************** BASE ********************************************/
			$arParams["PATH_TO_ICON"] = (empty($arParams["PATH_TO_ICON"]) ? $templateFolder."/images/icon/" : $arParams["PATH_TO_ICON"]);
			$arParams["PATH_TO_ICON"] = str_replace("//", "/", $arParams["PATH_TO_ICON"]."/");
			$arParams["SHOW_AUTHOR_COLUMN"] = ($arParams["SHOW_AUTHOR_COLUMN"] == "Y" ? "Y" : "N");
			$arParams["SHOW_RSS"] = ($arParams["SHOW_RSS"] == "N" ? "N" : "Y");
			if ($arParams["SHOW_RSS"] == "Y"):
				$arParams["SHOW_RSS"] = (!$USER->IsAuthorized() ? "Y" : (CForumNew::GetUserPermission($arParams["FID"], array(2)) > "A"? "Y" : "N"));
				if ($arParams["SHOW_RSS"] == "Y"):
					$APPLICATION->AddHeadString('<link rel="alternate" type="application/rss+xml" href="'.$arResult["URL"]["RSS_DEFAULT"].'" />');
				endif;
			endif;
			$arParams["~TMPLT_SHOW_ADDITIONAL_MARKER"] = trim($arParams["~TMPLT_SHOW_ADDITIONAL_MARKER"]);
			$arParams["SEO_USER"] = (in_array($arParams["SEO_USER"], array("Y", "N", "TEXT")) ? $arParams["SEO_USER"] : "Y");
			$arParams["USER_TMPL"] = '<noindex><a rel="nofollow" href="#URL#" title="'.GetMessage("F_USER_PROFILE").'">#NAME#</a></noindex>';
			if ($arParams["SEO_USER"] == "N") $arParams["USER_TMPL"] = '<a href="#URL#" title="'.GetMessage("F_USER_PROFILE").'">#NAME#</a>';
			elseif ($arParams["SEO_USER"] == "TEXT") $arParams["USER_TMPL"] = '#NAME#';
			$iIndex = rand();
			/********************************************************************
							/Input params
			********************************************************************/
			if (!empty($arResult["ERROR_MESSAGE"])): 
			?>
			<!--<div class="forum-note-box forum-note-error">
				<div class="forum-note-box-text"><?=ShowError($arResult["ERROR_MESSAGE"], "forum-note-error");?></div>
			</div>-->
			<p class="form-error-message">
				<i class="fa fa-exclamation-circle"></i>
				<?=$arResult["ERROR_MESSAGE"]?>
			</p>
			<?
			endif;
			if (!empty($arResult["OK_MESSAGE"])): 
			?>
			<!--<div class="forum-note-box forum-note-success">
				<div class="forum-note-box-text"><?=ShowNote($arResult["OK_MESSAGE"], "forum-note-success")?></div>
			</div>-->
			<p class="form-success-message">
				<i class="fa fa-check-circle"></i>
				<?=$arResult["OK_MESSAGE"]?>
			</p>
			<?
			endif;

			// *****************************************************************************************
			?>
			<!--<div class="forum-navigation-box forum-navigation-top">
				<div class="forum-page-navigation">
					<?/*=$arResult["NAV_STRING"]*/?>
				</div>
			<?/*
			if ($arResult["USER"]["RIGHTS"]["CAN_ADD_TOPIC"] == "Y"):
			*/?>
				<div class="forum-new-post">
					<noindex><a href="<?/*=$arResult["URL"]["TOPIC_NEW"]*/?>" title="<?/*=GetMessage("F_NEW_TOPIC_TITLE")*/?>" rel="nofollow"><span><?/*=GetMessage("F_NEW_TOPIC")*/?></span></a></noindex>
				</div>
			<?
			/*endif;*/
			?>
				<div class="forum-clear-float"></div>
			</div>-->

			<div class="forum-block raised-corners">
					<div class="forum-block-header">
						<div class="forum-header-options"><?
					if ($arParams["SHOW_RSS"] == "Y"):
					?>
							<span class="forum-option-feed"><noindex><a rel="nofollow" href="<?=$arResult["URL"]["RSS_DEFAULT"]?>" onclick="window.location='<?=addslashes(htmlspecialcharsbx($arResult["URL"]["~RSS"]))?>'; return false;">RSS</a></noindex></span>
					<?
					endif;
					if ($USER->IsAuthorized() && empty($arResult["USER"]["SUBSCRIBE"])):
						if ($arParams["SHOW_RSS"] == "Y"):
							?>&nbsp;&nbsp;<?
						endif;
						
					?>
						<!--<span class="forum-option-subscribe">
							<noindex><a rel="nofollow" title="<?=GetMessage("F_SUBSCRIBE_TO_NEW_POSTS")?>" href="<?=$APPLICATION->GetCurPageParam("ACTION=FORUM_SUBSCRIBE", 
								array("ACTION", "sessid"))?>"><?=GetMessage("F_SUBSCRIBE")?></a></noindex>
						</span>-->
					<?
					endif;
					?>
					
					<?$this->SetViewTarget('title');?>
					   <noindex><h2 class="nomargin-top"><?=$arResult["FORUM"]["NAME"]?></h2></noindex>
					<?$this->EndViewTarget();?> 
						</div>
						<h3>Темы:</h3>
					</div>
					
					<?
					if ($arResult["PERMISSION"] >= "Q"):
					?>
					<form class="forum-form" action="<?=POST_FORM_ACTION_URI?>" method="POST" onsubmit="return Validate(this)" name="TOPICS_<?=$iIndex?>" id="TOPICS_<?=$iIndex?>">
						<?=bitrix_sessid_post()?>
						<input type="hidden" name="PAGE_NAME" value="list" />
						<input type="hidden" name="NAV_PAGE" value="<?=$arResult['NAV_PAGE']?>" />
						<input type="hidden" name="FID" value="<?=$arParams["FID"]?>" />
					<?
					endif;
					?>
					<div class="forum-block-posts">
						<ul id='selectall'>
								
					<?
					if (empty($arResult["TOPICS"])):
					?>
								
											<div class="forum-empty-message"><?=GetMessage("F_NO_TOPICS_HERE")?><br />
					<?
					if ($arResult["USER"]["RIGHTS"]["CAN_ADD_TOPIC"] == "Y"):
					?>
											<?=str_replace("#HREF#", $arResult["URL"]["TOPIC_NEW"], GetMessage("F_CREATE_NEW_TOPIC"))?></div>
					<?
					endif;
					?>

											<div class="forum-footer-inner">&nbsp;
											</div>
										
					<?
					else:
					?>
								<!--<div class="forum-head-title"><span>
								<?/*=GetMessage("F_HEAD_TOPICS")*/?></span></div>-->
					<?
					if ($arParams["SHOW_AUTHOR_COLUMN"] == "Y"):
					?>
						<span><?=GetMessage("F_HEAD_AUTHOR")?></span>
					<?
					endif;
					?>
										<!--<span><?/*=GetMessage("F_HEAD_POSTS")*/?></span>
										<span><?/*=GetMessage("F_HEAD_VIEWS")*/?></span>
										<span><?/*=GetMessage("F_HEAD_LAST_POST")*/?></span>-->


					<?
					$iCount = 0;
					foreach ($arResult["TOPICS"] as $res):
						$iCount++;
					?> 
					
					<?
						$db_opros = CForumMessage::GetList(array("ID"=>"ASC"), array("TOPIC_ID"=>$res['ID'], 'PARAM1'=>'VT'));
						while ($ar_opros = $db_opros->Fetch())
						{
						  $opros = $ar_opros['PARAM1'];
						}
						
						if(CModule::IncludeModule("iblock"))
						{ 
							$ibpen = new CIBlockPropertyEnum;
							$db_res = $ibpen->GetList(Array(), Array('PROPERTY_ID'=>LIST_ID_THEMES,
															'XML_ID'=>$res['ID']));
							$arField = $db_res->Fetch();
							
							if(empty($arField['XML_ID'])){
								/*Проверяем есть ли фото*/
								$db_res = $ibpen->GetList(Array(), Array('PROPERTY_ID'=>LIST_ID_IMG_THEMES,
															'XML_ID'=>$res['ID']));
								$arField = $db_res->Fetch();
								
							}
						}
					?>
					
					<?
						$arGrpUsr = $USER->GetUserGroupArray();
						$arRez = array_intersect($arGrpUsr, $arPERM);
						if(empty($arGrpUsr)){
							echo "шаблон diva\forum.topic.list template.php arGrpUsr - пустой";
						}
					?>
					
										<li <?if(!empty($arRez)):?>ondblclick="defaulticoTHEMES(this);"<?endif?> class="<?=($res['TopicStatus'] == 'NEW'? 'has-message' : ' ')?>">	
												<div class="forum-icon">
													<?if(empty($arField['XML_ID'])):?>
															<i <?if(!empty($arRez)):?>ondblclick="selecticoTHEMES(this, event);"<?endif?> id="THEMES<?=$res["ID"]?>" class="fa fa-<?
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
															elseif ($res["TopicStatus"] == "NEW" && empty($opros)):
																$title .= (empty($title) ? GetMessage("F_HAVE_NEW_MESS") : " (".GetMessage("F_HAVE_NEW_MESS").")");
																?>file-o<?
															elseif (empty($opros)):
																$title .= (empty($title) ? GetMessage("F_NO_NEW_MESS") : "");
																?>file-o<?
															endif;
															
															?>" title="<?=$title?>"><!-- ie --></i>
													<?endif?>
													
													<?if($arField['PROPERTY_CODE'] === 'PROP_TEMES_ICO'):?>
														<i id="THEMES<?=$res['ID']?>" class="<?=$arField['VALUE']?>" <?if(!empty($arRez)):?>ondblclick="selecticoTHEMES(this, event);"<?endif?>></i>
													<?endif?>
													
													<?if($arField['PROPERTY_CODE'] === 'PROP_TEMES_IMG'):?>
														<img id="THEMES<?=$res['ID']?>" src="<?=$arField['VALUE']?>" <?if(!empty($arRez)):?>ondblclick="selecticoTHEMES(this, event);"<?endif?>></i>
													<?endif?>
											
											</div>
										
											
											<?if ($arResult["PERMISSION"] >= "Q"):?>
											<div class='control-box'>	
														<input class="control-box-checkbox" type="checkbox" name="TID[]" value="<?=$res["ID"]?>" onclick="SelectRow(this.parentNode.parentNode)" />
											</div>	
											<?endif;?>
											
											<div class="forum-description">
												<div class="forum-view-table">
													<div class = "heading">
														<h4>
														<?if (intval($res["SORT"]) != 150 && $res["STATE"]!="Y"):
																		?>
																	<?if ($res["STATE"] != "L"):
																		?><i class="fa fa-lock"></i>
																	<?endif?>
																		<strong><?=GetMessage("F_PINNED")?>, <?
																	if ($res["STATE"] != "L"):
																		?><?=GetMessage("F_CLOSED")?>:&nbsp;</strong><?
																	else:
																		?><?=GetMessage("F_MOVED")?>:&nbsp;</strong><?
																	endif;
																elseif ($res["TopicStatus"] == "MOVED" || $res["STATE"]=="L"):
																		?><strong><?=GetMessage("F_MOVED")?>:&nbsp;</strong><?
																elseif (($opros || $res["STATE"]=="L") && $res["STATE"]=="Y"):
																		?><strong><?=GetMessage("OPROS")?>:&nbsp;</strong><?
																elseif (intVal($res["SORT"]) != 150):
																		?><strong><?=GetMessage("F_PINNED")?>:&nbsp;</strong><?
																elseif (($res["STATE"]!="Y") && ($res["STATE"]!="L")):
																		?><i class="fa fa-lock"></i>
																		<strong><?=GetMessage("F_CLOSED")?>:&nbsp;</strong><?
																elseif ($opros && ($res["STATE"]=="N")):
																		?><i class="fa fa-lock"></i>
																		<strong><?=GetMessage("F_CLOSED")?>:&nbsp;</strong><?
																endif;
																		?>
																		
																		<span class="forum-item-title"><?
																if (false && strLen($res["IMAGE"]) > 0):
																		?><img src="<?=$arParams["PATH_TO_ICON"].$res["IMAGE"];?>" alt="<?=$res["IMAGE_DESCR"];?>" border="0" width="15" height="15"/><?
																endif;
																		?><a href="<?=$res["URL"]["TOPIC"]?>" title="<?=GetMessage("F_TOPIC_START")?> <?=$res["START_DATE"]?>"><?=$res["TITLE"]?></a><?
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
										<?
																if (!empty($res["DESCRIPTION"])):
										?>
																<!--	<span class="forum-item-desc"><?/*=$res["DESCRIPTION"]*/?></span><span class="forum-item-desc-sep"><?
																	?><?/*=($arParams["SHOW_AUTHOR_COLUMN"] != "Y" ? "&nbsp;&middot; " : "")*/?></span>-->
										<?
																endif;?>
														</h4>
														
										<?					if ($arParams["SHOW_AUTHOR_COLUMN"] != "Y"):
									?>				<p>
																<?$creatData = EditData ($res['~START_DATE']);?>
																<?=GetMessage("F_AUTHOR")?>&nbsp;<a href='<?=$res['URL']['USER_START']?>'><?=$res["USER_START_NAME"]?></a>
																&raquo; <?=$creatData?>
														</p>
									<?
															endif;
									?>
														
									<?
															if ($arParams["SHOW_AUTHOR_COLUMN"] == "Y"):
									?>
														<span><?
															if ($res["USER_START_ID"] > 0):
																?><?=str_replace(array("#URL#", "#NAME#"), array($res["URL"]["USER_START"], $res["USER_START_NAME"]), $arParams["USER_TMPL"]);
															else:
																?><?=$res["USER_START_NAME"]?><?
															endif;
														?></span>
									<?
															endif; ?>
									<?						if ($res["LAST_MESSAGE_ID"] > 0):
							?>
														<p>
														
															<span class="visible-800">
																<i class="fa fa-comment-o"></i>
																<?$answer = inclination($res["numMessages"], array("ответ","ответа","ответов"));?>
																<strong><?=$res["numMessages"]?> <?=$answer?>, </strong>
																последний от
															</span>
															<?$crtDataPst = EditData($res["LAST_POST_DATE"]);?>
															<span class="invisible-800"><?=GetMessage("LAST_POST")?></span> <noindex><a  href="<?=$res["URL"]["LAST_POSTER"]?>"><?=$res["LAST_POSTER_NAME"]?></a> &raquo; <?=$crtDataPst ?></noindex>
															
														</p>
								<?
														else:
								?>
														&nbsp;
								<?
														endif;
							?>
														<?$opros = false?>
										</div><!--/heading-->
										<div class="post-count">
							<?						if ($arResult["PERMISSION"] >= "Q" && $res["mCnt"] > 0):
							?>
														<i class="fa fa-comments"></i>
														<?
															
														?>
														<?$answer = inclination($res["numMessages"], array("ответ","ответа","ответов"));?>
														<?=$res["numMessages"]?> <?=$answer?>
														<?
															?>(<noindex><a rel="nofollow" href="<?=$res["URL"]["MODERATE_MESSAGE"]?>" title="<?=GetMessage("F_MESSAGE_NOT_APPROVED")?>"><?=$res["mCnt"]?></a></noindex>)
									<?
															else:
									?>		
														<?$answer = inclination($res["numMessages"], array("ответ","ответа","ответов"));?>
														<i class="fa fa-comments"></i>
														<?=$answer?> <?=$res["numMessages"]?>
									<?
													endif;
							?>
												<!--<span><?/*=$res["VIEWS"]*/?></span>-->
										</div><!--post-count-->			
									</div><!--/forum-view-table-->
								</div><!--/forum-description-->
									
									</li>	
					<?
							endforeach;
					?>
								
									
								
					<?
					endif;
					?>
							
						</ul>
						

					<?
											if ($arResult["PERMISSION"] >= "Q"):
					?>						<div class="forum-admin-controls clear">
																														
							<?					if ($USER->IsAuthorized()):
									?>
														<noindex><a rel="nofollow" <?
														?>href="<?=$APPLICATION->GetCurPageParam("ACTION=SET_BE_READ", array("ACTION", "sessid"))?>" <?
														?>onclick="return this.href+=('&sessid='+BX.bitrix_sessid());"><?=GetMessage("F_SET_FORUM_READ")?></a></noindex>
									<?
													endif;
													
													if ($arResult["PERMISSION"] >= "Q"):
									?>
																<noindex><a id="check_all" rel="nofollow" href="javascript:void(0);"  name=""><?=GetMessage("F_SELECT_ALL")?></a></noindex>
									<?
															elseif (!$USER->IsAuthorized()):
									?>
																&nbsp;
																
													<?
													endif;
													?>
													<select name="ACTION" class="inputbox">
														<option value=""><?=GetMessage("F_MANAGE_TOPICS")?></option>
														<option value="SET_TOP"><?=GetMessage("F_MANAGE_PIN")?></option>
														<option value="SET_ORDINARY"><?=GetMessage("F_MANAGE_UNPIN")?></option>
														<option value="STATE_Y"><?=GetMessage("F_MANAGE_OPEN")?></option>
														<option value="STATE_N"><?=GetMessage("F_MANAGE_CLOSE")?></option>
														<option value="MOVE_TOPIC"><?=GetMessage("F_MANAGE_MOVE")?></option>
					<?
													if ($arResult["PERMISSION"] >= "U"):
							?>
																<option value="DEL_TOPIC"><?=GetMessage("F_MANAGE_DELETE")?></option>
							<?
													endif;
							?>
															</select>&nbsp;<input  class="btn gray" type="submit" value="OK" />
													
												</div>	<!--/forum-admin-controls clear-->
											<?endif;?>	
						
						
					</div><!--forum-block-posts-->
					<?
					if ($arResult["PERMISSION"] >= "Q"):
					?>
					
					</form>
			</div> <!--/forum-block raised-corners-->
			<?
			endif;
			?>
			<?if(!$USER->IsAuthorized()):?>
				</div><!--/forum-block raised-corners For not Autorize users-->
			<?endif?>	

			<?if($USER->IsAuthorized() && !($arResult["PERMISSION"] >= "Q")):?>
				</div><!--/forum-block raised-corners For  Autorize users-->
			<?endif?>

			<div class="forum-controls">
			<?
			if ($arResult["USER"]["RIGHTS"]["CAN_ADD_TOPIC"] == "Y"):
			?>
				<div class="forum-reply-button float-right">
					<noindex><a class="btn green" rel="nofollow" href="<?=$arResult["URL"]["TOPIC_NEW"]?>" title="<?=GetMessage("F_NEW_TOPIC_TITLE")?>"><span><?=GetMessage("F_NEW_TOPIC")?></span></a></noindex>
				</div>
			<?
			endif;
			?>
				<div class='forum-pagination'>
					<div class="dropdown-container">
						<a class="dropdown-trigger" data-toggle="dropdown" href="#">
							Страницы
							<i></i>
						</a>
						<div class="cat-selector dropdown-menu">
							<?=$arResult["NAV_STRING"]?>
						</div>
					</div>
					<div class="clear"></div>
				</div>
			</div>
			<?

			if (!empty($arResult["ERROR_MESSAGE"])): 
			?>
			<!--<div class="forum-note-box forum-note-error">
				<div class="forum-note-box-text"><?=ShowError($arResult["ERROR_MESSAGE"], "forum-note-error");?></div>
			</div>-->
			<p class="form-error-message">
				<i class="fa fa-exclamation-circle"></i>
				<?=$arResult["ERROR_MESSAGE"]?>
			</p>
			<?
			endif;
			if (!empty($arResult["OK_MESSAGE"])): 
			?>
			<!--<div class="forum-note-box forum-note-success">
				<div class="forum-note-box-text"><?=ShowNote($arResult["OK_MESSAGE"], "forum-note-success")?></div>
			</div>-->
			<p class="form-success-message">
				<i class="fa fa-check-circle"></i>
				<?=$arResult["OK_MESSAGE"]?>
			</p>
			<?
			endif;

			?>
			<script>
			if (typeof oText != "object")
					var oText = {};
			oText['empty_action'] = '<?=CUtil::addslashes(GetMessage("JS_NO_ACTION"))?>';
			oText['empty_topics'] = '<?=CUtil::addslashes(GetMessage("JS_NO_TOPICS"))?>';
			oText['del_topics'] = '<?=CUtil::addslashes(GetMessage("JS_DEL_TOPICS"))?>';
			</script>
			
			<?if(!empty($arRez)):?>
						<script>
							function selecticoTHEMES(el, event)
							{
								if($('#cancelicoTHEMES').length > 0)
								{
									$('#cancelicoTHEMES').remove();
								}
								var FILE_SIZE = 30000; //байты
								/*Отменяем всплытие*/
								event = event || window.event; // Кроссбраузерно получить событие
									
									if (event.stopPropagation) { // существует ли метод?
									// Стандартно:
									event.stopPropagation();
									} else {
									// Вариант IE
									event.cancelBubble = true;
									}

								var formOld = $('#blockicoTHEMES');
								if(formOld.length > 0)
								{
									$('#icosaveTHEMES').unbind();
									formOld.remove(); 
								}	
								var form =
								'<div id="blockicoTHEMES">\
									<input id="icotxtTHEMES" type="text"/>\
									<input id="icosaveTHEMES" type="button" value="OK"/>\
									<input id="icofileTHEMES" type="file" name="icofile" accept="image/*"/>\
									<input id="submitTHEMES" type="button" value="Отправить файл"/>\
									<input id="icocancelTHEMES" type="button" value="отмена" \
													onclick="icocancelTHEMES();"/>\
								</div>';
								
								$(el).after(form);

								$('#icosaveTHEMES').bind('click', el, saveicoTHEMES);
								$('#submitTHEMES').bind('click', el, filesubmitTHEMES);
							}
							
							function saveicoTHEMES(el)
							{
								var id = el.data.id.replace('THEMES', '');
								var txt = $('#icotxtTHEMES').val();

								BX.ajax.get('?key=icoselectTHEMES&txt='+txt+'&id='+id);
								
								$('#blockicoTHEMES').remove();
								
								var load = '<i id="load" class="fa fa-spinner fa-spin"></i>';
								$(el.data).replaceWith(load);
							}
							
							function defaulticoTHEMES(el)
							{
								if($('#blockicoTHEMES').length > 0)
								{
									$('#blockicoTHEMES').remove();
								}
								
								if($('#cancelicoTHEMES').length < 1)
								{
									var form = '<div id="cancelicoTHEMES">\
															<label>Сделать иконку по умолчанию?</label>\
															<input id="icodefokTHEMES" type="button" value="OK"/>\
															<input id="icodefcancelTHEMES" type="button" value="отмена" \
																			onclick="icodefcancelTHEMES();"/>\
														</div>';
									$(el).append(form);

									$('#icodefokTHEMES').bind('click', el, icodefokTHEMES);
								}
							}
							
							function icodefokTHEMES(el)
							{
								var id = $(el.data).children().children()[0].id.replace('THEMES', '');
								var txt = 'fa fa-files-o';
								BX.ajax.get('?key=icoselectTHEMES&txt='+txt+'&id='+id);
								
								$('#cancelicoTHEMES').remove();
								
								var load = '<i id="load" class="fa fa-spinner fa-spin"></i>';
								$($(el.data).children().children()[0]).replaceWith(load);
							}
							
							function icocancelTHEMES()
							{
								var formOld = $('#blockicoTHEMES');
								$('#icosaveTHEMES').unbind();
								$('#submitTHEMES').unbind();
								formOld.remove(); 
							}
							
							function icodefcancelTHEMES()
							{
								var formOld = $('#cancelicoTHEMES');
								$('#icodefokTHEMES').unbind();
								formOld.remove(); 
							}
							
							function filesubmitTHEMES(el)
							{
								var con = document.getElementById('icofileTHEMES');

								var f = new BX.ajax.FormData();
								f.append('key', 'fileicoTHEMES');
								f.append('id', el.data.id.replace('THEMES', ''));
								f.append('file', con.files[0]);
								f.send(' ');
								
								var formOld = $('#blockicoTHEMES');
								formOld.remove(); 
								
								var load = '<i id="load" class="fa fa-spinner fa-spin"></i>';
								$(el.data).replaceWith(load);
							}
						</script>
				<?endif?>
<?endif?>	

<?if($_GET['key'] == 'icoselectTHEMES'):?>

			<script>
				var ico = '<i id="THEMES<?=$_GET['id']?>" class="<?=$_GET['txt']?>" ondblclick="selecticoTHEMES(this, event);"></i>'
				$('#load').replaceWith(ico);
			
			
			<?
				
				 if(CModule::IncludeModule("iblock"))
				{ 
					$ibpenum = new CIBlockPropertyEnum;
					
					/*Проверяем фото*/
					$db_resIMG = $ibpenum->GetList(Array(), Array('PROPERTY_ID'=>LIST_ID_IMG_THEMES,
													'XML_ID'=>$_GET['id']));
					$arFieldIMG = $db_resIMG->Fetch();	
					
					if(!empty($arFieldIMG['XML_ID'])){
							$ibpenum->Delete($arFieldIMG['ID']);
					}
					
					/*Работаем с иконками*/
					$db_res = $ibpenum->GetList(Array(), Array('PROPERTY_ID'=>LIST_ID_THEMES,
													'XML_ID'=>$_GET['id']));
					$arField = $db_res->Fetch();
					
					if(empty($arField['XML_ID']))
					{
					
						$PropID = $ibpenum->Add(Array('PROPERTY_ID'=>LIST_ID_THEMES,  
							'XML_ID'=>$_GET['id'],'VALUE'=>$_GET['txt']));
					}
					else
					{
						$PropID = $ibpenum->Update($arField['ID'], Array('VALUE'=>$_GET['txt']));
					}
				}
			?>
			</script>	
		<?endif?>

<?if($_POST['key'] == 'fileicoTHEMES'):?>	
			<script>
				<?if(empty($_FILES['file']['name'])):?>
					var ico = '<i id="THEMES<?=$_POST['id']?>" class="fa fa-files-o"\
										ondblclick="selecticoTHEMES(this, event);"></i>'
					$('#load').replaceWith(ico);
				<?else:?>
					<?
					
						$arFile = Array(
										"name" => $_FILES['file']['name'],
										"size" => $_FILES['file']['size'],
										"tmp_name" => $_FILES['file']['tmp_name'],
										"type" => $_FILES['file']['tmp_name']
										);
						
						if(!empty($arFile['name']))
						{
							$IDfile = CFile::SaveFile($arFile, 'forum');
							$path = CFile::ResizeImageGet(
								$IDfile,
								array('width'=>35, 'height'=>35),
								BX_RESIZE_IMAGE_PROPORTIONAL
							);
						}
					?>
					var img = '<img id="THEMES<?=$_POST['id']?>" src="<?=$path['src']?>"\
										ondblclick="selecticoTHEMES(this, event);">';
					$('#load').replaceWith(img);
					
					<? if(CModule::IncludeModule("iblock"))
						{ 
							$ibpenum = new CIBlockPropertyEnum;
							
							/*Проверяем иконки*/
							$db_resICO = $ibpenum->GetList(Array(), Array('PROPERTY_ID'=>LIST_ID_THEMES,
															'XML_ID'=>$_POST['id']));
							$arPSFieldICO = $db_resICO->Fetch();
							
							if(!empty($arPSFieldICO['XML_ID'])){
								$ibpenum->Delete($arPSFieldICO['ID']);
							}
							
							/*Работаем с фото*/
							$db_res = $ibpenum->GetList(Array(), Array('PROPERTY_ID'=>LIST_ID_IMG_THEMES,
															'XML_ID'=>$_POST['id']));
							$arPSField = $db_res->Fetch();
							
							if(empty($arPSField['XML_ID']))
							{
								
								$PropID = $ibpenum->Add(Array('PROPERTY_ID'=>LIST_ID_IMG_THEMES,  
									'XML_ID'=>$_POST['id'],'VALUE'=>$path['src']));
							}
							else
							{
								$PropID = $ibpenum->Update($arPSField['ID'], Array('VALUE'=>$path['src']));
							}
						}
					?>
					
				<?endif?>
			</script>

<?endif?>				