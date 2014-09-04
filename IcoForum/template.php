<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

define("LIST_ID", 84);
define("LIST_ID_IMG", 85);
define("IBLOCK_ID", 17); 

if($_GET['key'] != 'icoselect' || $_POST['key'] != 'fileico')
{
		if (!$this->__component->__parent || empty($this->__component->__parent->__name)):
			$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/components/bitrix/forum/templates/.default/style.css');
			$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/components/bitrix/forum/templates/.default/themes/blue/style.css');
			$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/components/bitrix/forum/templates/.default/styles/additional.css');
		endif;
		IncludeAJAX();
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

		/***************** BASE ********************************************/
		$arParams["WORD_WRAP_CUT"] = intVal($arParams["WORD_WRAP_CUT"]);
		$arParams["SHOW_RSS"] = ($arParams["SHOW_RSS"] == "N" ? "N" : "Y");
		$arParams["SHOW_RSS"] = ($arParams["SHOW_RSS"] == "Y" && !empty($arResult["FORUMS_FOR_GUEST"]) ? "Y" : "N");
		if ($arParams["SHOW_RSS"] == "Y"):
			$APPLICATION->AddHeadString('<link rel="alternate" type="application/rss+xml" href="'.$arResult["URL"]["RSS_DEFAULT"].'" />');
		endif;
		$arResult["USER"]["HIDDEN_GROUPS"] = explode("/", $_COOKIE[COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_FORUM_GROUP"]);
		$arParams["TMPLT_SHOW_ADDITIONAL_MARKER"] = trim($arParams["TMPLT_SHOW_ADDITIONAL_MARKER"]);
		/********************************************************************
						/Input params
		********************************************************************/

		?>
		<!--<div class="forum-header-box">-->
		<?
		if (!empty($arParams["GID"])):
		?>
		<!--<div class="forum-header-box">
			<div class="forum-header-options">
				<span class="forum-option-feed"><a href="<?=$arResult["URL"]["INDEX"]?>"><?=GetMessage("F_FORUMS")?></a></span>
			</div>
			<div class="forum-header-title"><span><?=$arResult["GROUP"]["NAME"]?></span></div>
		</div>-->
		<?
		else:
		?>
			<!--<div class="forum-header-title"><span><?/*=GetMessage("F_FORUMS")*/?></span></div>-->
		<?
		endif;
		?>
		<!--</div>-->

		<?$GLOBALS['topLevel'] = false;
			$title = NULL;?>



		<?
		if (!empty($arResult["FORUMS"]["FORUMS"]) || ($arResult["GROUP"]["ID"] > 0 && !empty($arResult["FORUMS"]["GROUPS"][$arResult["GROUP"]["ID"]]["FORUMS"]))):

		$GLOBALS['topLevel'] = true;
		?>

		<?$this->SetViewTarget('title');?>
		   <?$title=(empty($arResult["GROUP"]["NAME"]) ? "Форум города Спутник"  : $arResult["GROUP"]["NAME"])?>
		   <noindex><h2><?=$title?></h2></noindex>
		<?$this->EndViewTarget();?> 

		<div class="forum-block raised-corners">
					<div class="forum-block-header">
						<h3><?=empty($arResult["GROUP"]["NAME"]) ? "Важное" : "Razdel"/*$arResult["GROUP"]["NAME"]*/?></h3>
					</div>
		<div class="forum-block-posts">				
		<ul>
		<?
		endif;
		if (!function_exists("__PrintForumGroupsAndForums"))
		{
			function __PrintForumGroupsAndForums($arRes, $arResult, $arParams, $depth = -1)
			{
				static $bInsertSeparator = false;
			
				$arGroup = $arRes;
				if (!is_array($arRes))
					return false;

				if (intVal($arGroup["ID"]) > 0 && $arGroup["ID"] != $arResult["GROUP"]["ID"])
				{
					if ($bInsertSeparator):
		?>
				
		<?
					endif;
		?>
		<?if(empty($arResult["GROUP"]["ID"])):?>
				<div class="forum-block raised-corners">
							<div class="forum-block-header">
									<h3><?=$arGroup["NAME"]?></h3>
							</div>
							
							<div class="forum-block-posts">
								<ul>

		<?endif?>						
					<?
								$bInsertSeparator = true;
				}
				$iCountRows = 1;
				if (array_key_exists("FORUMS", $arRes))
				{
					?>
					
					<?
								foreach ($arGroup["FORUMS"] as $res)
								{
									
									if ($arParams["WORD_WRAP_CUT"] > 0):
										$res["TITLE"] = (strLen($res["~TITLE"]) > $arParams["WORD_WRAP_CUT"] ? 
											htmlspecialcharsEx(substr($res["~TITLE"], 0, $arParams["WORD_WRAP_CUT"]))."..." : $res["TITLE"]);
										$res["LAST_POSTER_NAME"] = (strLen($res["~LAST_POSTER_NAME"]) > $arParams["WORD_WRAP_CUT"] ? 
											htmlspecialcharsEx(substr($res["~LAST_POSTER_NAME"], 0, $arParams["WORD_WRAP_CUT"]))."..." : $res["LAST_POSTER_NAME"]);
									endif;
					?>
					
					<li ondblclick="defaultico(this);">
								<div class="forum-icon">
								<?
									 if(CModule::IncludeModule("iblock"))
									{ 
										$ibpen = new CIBlockPropertyEnum;
										$db_res = $ibpen->GetList(Array(), Array('IBLOCK_ID'=>IBLOCK_ID,
																		'XML_ID'=>$res['ID']));
										$arField = $db_res->Fetch();
									}
								?>
								<?if(empty($arField['XML_ID'])):?>	
									<i id="<?=$res['ID']?>" class="fa fa-files-o" ondblclick="selectico(this, event);"></i>
								<?endif?>
								
								<?if($arField['PROPERTY_CODE'] === 'PROP_ICO'):?>
									<i id="<?=$res['ID']?>" class="<?=empty($arField['XML_ID'])? 'fa fa-files-o' : $arField['VALUE']?>" ondblclick="selectico(this, event);"></i>
								<?endif?>
								
								<?if($arField['PROPERTY_CODE'] === 'PROP_IMG'):?>
									<img id="<?=$res['ID']?>" src="<?=$arField['VALUE']?>" ondblclick="selectico(this, event);"></i>
								<?endif?>
		<?
						/*if ($res["NewMessage"] == "Y")
						{
		?>
									<div class="forum-icon forum-icon-newposts" title="<?=GetMessage("F_HAVE_NEW_MESS")?>"><!-- ie --></div>
		<?
						}
						else
						{
		?>
									<div class="forum-icon forum-icon-default" title="<?=GetMessage("F_NO_NEW_MESS")?>"><!-- ie --></div>
		<?
						}*/
		?>
								</div>
							
							
								<div class="forum-description">
									<div class="forum-view-table forum-category">
										<div class="heading with-icon icon-category">
										<h4><a href="<?=$res["URL"]["TOPICS"]?>"><?
											?><?=$res["~NAME"];?></a><?
							if ($res["NewMessage"] == "Y" && strLen($arParams["TMPLT_SHOW_ADDITIONAL_MARKER"]) > 0):
											?><noindex><a rel="nofollow" href="<?=$res["URL"]["TOPICS"]?>" class="forum-new-message-marker"><?=$arParams["TMPLT_SHOW_ADDITIONAL_MARKER"]?></a></noindex><?
							endif;
											?></h4>
										<p><?=$res["~DESCRIPTION"]?></p>

										<!--<p class="subcategory">-->
											
											<?/*
												$db_res = CForumTopic::GetList(array("SORT"=>"ASC", "LAST_POST_DATE"=>"DESC"), array("FORUM_ID"=>$res["ID"]));
												$cntTems = 0;
												if($res['TOPICS'])
												{
												?>
													<b>Подразделы: </b>
												<?
												}
												while ($ar_res = $db_res->Fetch())
												{
													$arTopic = CForumTopic::GetByID($ar_res["ID"]);
													if ($arTopic)
													{
													  $url = $res["URL"]["TOPICS"].$arTopic["TITLE_SEO"];
													}
												++$cntTems;
											*/?>
												<!--<span>
													<i class="fa fa-folder-o"></i>
													<a href="<?=$url ?>"><?=$ar_res["TITLE"]?> <?=$cntTems?></a>
												</span>-->
												<?/*if($res['TOPICS'] > $cntTems):?>
													<?=', '?>
												<?endif*/?>	
							<?		
								/*}*/
							?>
										<!--</p>-->
										<p class="visible-800">
											<strong><?=$res["TOPICS"]?> тем, <?=$res["POSTS"]?> сообщений</strong>
										</p>
			<?
							if ($res["PERMISSION"] >= "Q" && ($res["MODERATE"]["TOPICS"] > 0 || $res["MODERATE"]["POSTS"] > 0)):
			?>
										<div class="forum-moderator-stat"><?=GetMessage("F_NOT_APPROVED")?>&nbsp;<?
										if ($res["MODERATE"]["TOPICS"] > 0):
											?><?=GetMessage("F_NOT_APPROVED_TOPICS")?>:&nbsp;<span><?=$res["MODERATE"]["TOPICS"]?></span><?=($res["MODERATE"]["POSTS"] > 0 ? ", " : "")?><?
										endif;
										if ($res["MODERATE"]["POSTS"] > 0):
											?><?=GetMessage("F_NOT_APPROVED_POSTS")?>:&nbsp;<span><?
												?><noindex><a rel="nofollow" href="<?=$res["URL"]["MODERATE_MESSAGE"]?>"><?=$res["MODERATE"]["POSTS"]?></a></noindex></span><?
										endif;
							endif;
			?>						
										</div>
							
								<div class="themes-count">
									<b><?=$res["TOPICS"]?></b>
									<br>
										<?$topic = inclination($res["TOPICS"], array('F_NOT_APPROVED_TOPICS_1', 'F_NOT_APPROVED_TOPICS_1_1',  'F_NOT_APPROVED_TOPICS_1_1_1')) ?>
										<?=GetMessage($topic)?>
								</div>
								
								<div class="post-count">
									<b><?=$res["POSTS"]?></b>
									<br>
										<?$answers = inclination($res["POSTS"], array('F_NOT_APPROVED_POSTS_2', 'F_NOT_APPROVED_POSTS_2_2',  'F_NOT_APPROVED_POSTS_2_2_2')) ?>
										<?=GetMessage($answers)?>
								</div>
							</div>
						</div>
					</li>		
							
		<?
							if (intVal($res["LAST_MESSAGE_ID"]) > 0):
		?>
								<!--<div class="forum-lastpost-box">
									<span class="forum-lastpost-title"><?
										?><noindex><a rel="nofollow" href="<?/*=$res["URL"]["MESSAGE"]*/?>" title="<?/*=htmlspecialcharsEx($res["~TITLE"]." (".$res["~LAST_POSTER_NAME"].")")*/?>"><?
											?><?/*=$res["TITLE"]*/?> <span class="forum-lastpost-author">(<?/*=$res["LAST_POSTER_NAME"]*/?>)</span></a></noindex></span>
									<span class="forum-lastpost-date"><?/*=$res["LAST_POST_DATE"]*/?></span>
								</div>-->
		<?
							else:
		?>
								<!--&nbsp;-->
		<?
							endif;
		?>
							
		<?
						$iCountRows++;
					}/*endForeach*/
		?>



		<?
				}/*Если есть форумы*/
				
				
				$iCountRows = 0;
				if (array_key_exists("GROUPS", $arRes)):
				
					if ($depth >= 1)
					{
		?>

		<?
						foreach ($arRes["GROUPS"] as $key => $res)
						{
							$iCountRows++;
							
		?>			
								<li>
									<div class="forum-icon">
										<i class="fa fa-files-o"></i>
		<?
						/*if ($res["NewMessage"] == "Y")
						{
		?>
									<div class="forum-icon forum-icon-newposts" title="<?=GetMessage("F_HAVE_NEW_MESS")?>"><!-- ie --></div>
		<?
						}
						else
						{
		?>
									<div class="forum-icon forum-icon-default" title="<?=GetMessage("F_NO_NEW_MESS")?>"><!-- ie --></div>
		<?
						}*/
		?>
								</div>
							
						
								<div class="forum-description">
									<div class="forum-view-table forum-category">
										<div class="heading with-icon icon-category">
										<h4><?
									?><noindex><a rel="nofollow" href="<?=$arResult["URL"]["GROUP_".$res["ID"]]?>"><?
										?><?=$res["~NAME"];?></a></noindex></h4>
									
						
									<?if (array_key_exists("GROUPS", $res)):
										?>
										<p  class="subcategory">
												<b><?=GetMessage("F_SUBGROUPS")?></b> <?
												$bFirst = true;
												$cnt = 1;
												foreach ($res["GROUPS"] as $val):
													if (!$bFirst):
														?>, <?
													endif;
													?>
													
													<span>	
															<i class="fa fa-folder-o"></i>
															<noindex><a rel="nofollow" href="<?=$arResult["URL"]["GROUP_".$val["ID"]]?>"><?=$val["~NAME"]?> <?=$cnt?></a></noindex>
													</span>
													
													
												<?$bFirst = false;
													$cnt++;
												endforeach;?>
												
												<?foreach ($res["FORUMS"] as $val):
													if (!$bFirst):
														?>, <?
													endif;
													?>
													
													<span>	
															<i class="fa fa-folder-o"></i>
															<noindex><a rel="nofollow" href="<?=$val["URL"]["TOPICS"]?>"><?=$val["~NAME"]?> <?=$cnt?></a></noindex>
													</span>
													
													
												<?$bFirst = false;
													$cnt++;
												endforeach;?>
												
										</p>	
										
									<?else:?>	
									
										<p  class="subcategory">
												<b><?=GetMessage("F_SUBGROUPS")?></b> <?
												$bFirst = true;
												$cnt = 1;
												foreach ($res["GROUPS"] as $val):
													if (!$bFirst):
														?>, <?
													endif;
													?>
													
													<span>	
															<i class="fa fa-folder-o"></i>
															<noindex><a rel="nofollow" href="<?=$arResult["URL"]["GROUP_".$val["ID"]]?>"><?=$val["~NAME"]?> <?=$cnt?></a></noindex>
													</span>
													
													
												<?$bFirst = false;
													$cnt++;
												endforeach;?>
												
												<?foreach ($res["FORUMS"] as $val):
													if (!$bFirst):
														?>, <?
													endif;
													?>
													
													<span>	
															<i class="fa fa-folder-o"></i>
															<noindex><a rel="nofollow" href="<?=$val["URL"]["TOPICS"]?>"><?=$val["~NAME"]?> <?=$cnt?></a></noindex>
													</span>
													
													
												<?$bFirst = false;
													$cnt++;
												endforeach;?>
												
										</p>	
																	
									<?endif;?>
										
									
										<p class="visible-800">
											<strong><?=$res["TOPICS"]?> тем, <?=$res["POSTS"]?> сообщений</strong>
										</p>
									<?
													if ($res["MODERATE"]["TOPICS"] > 0 || $res["MODERATE"]["POSTS"] > 0):
									?>
																<div class="forum-moderator-stat"><?=GetMessage("F_NOT_APPROVED")?>&nbsp;<?
																if ($res["MODERATE"]["TOPICS"] > 0):
																	?><?=GetMessage("F_NOT_APPROVED_TOPICS")?>:&nbsp;<span><?=$res["MODERATE"]["TOPICS"]?></span><?
																		?><?=($res["MODERATE"]["POSTS"] > 0 ? ", " : "")?><?
																endif;
																if ($res["MODERATE"]["POSTS"] > 0):
																	?><?=GetMessage("F_NOT_APPROVED_POSTS")?>:&nbsp;<span><?
																		?><noindex><a rel="nofollow" href="<?=$arResult["URL"]["GROUP_".$res["ID"]]?>"><?
																		?><?=$res["MODERATE"]["POSTS"]?></a></noindex></span><?
																endif;
													endif;
									?>
										</div>
										
									<div class="themes-count">
											<b><?=$res["TOPICS"]?></b>
											<br>
											тем<?$topic = inclination($res["TOPICS"], array('F_NOT_APPROVED_TOPICS_1', 'F_NOT_APPROVED_TOPICS_1_1',  'F_NOT_APPROVED_TOPICS_1_1_1')) ?>
												<?/*=GetMessage($topic)*/?>
										</div>
										
										<div class="post-count">
											<b><?=$res["POSTS"]?></b>
											<br>
											ответов<?$answers = inclination($res["POSTS"], array('F_NOT_APPROVED_POSTS_2', 'F_NOT_APPROVED_POSTS_2_2',  'F_NOT_APPROVED_POSTS_2_2_2')) ?>
												<?/*=GetMessage($answers)*/?>
										</div>
									</div>
								</div>
							
					</li>
							
		<?
							if (intVal($res["LAST_MESSAGE_ID"]) > 0):
		?>
								<!--<div class="forum-lastpost-box">
									<span class="forum-lastpost-title"><?
										?><noindex><a rel="nofollow" href="<?/*=$res["URL"]["MESSAGE"]*/?>" title="<?/*=htmlspecialcharsEx($res["~TITLE"]." (".$res["~LAST_POSTER_NAME"].")")*/?>"><?
											?><?/*=$res["TITLE"]*/?> <span class="forum-lastpost-author">(<?/*=$res["LAST_POSTER_NAME"]*/?>)</span></a></noindex></span>
									<span class="forum-lastpost-date"><?/*=$res["LAST_POST_DATE"]*/?></span>
								</div>-->
		<?
							else:
		?>
								<!--&nbsp;-->
		<?
							endif;
		?>


		<?
						}/*endforeach*/
		?>
					

			
		<?
					} /*$depth >= 1*/
					else 
					{
						$depth++;
						
						foreach ($arRes["GROUPS"] as $key => $val)
						{
							if(empty($arResult["GROUP"]["ID"])):
								if ($GLOBALS['topLevel'] ):
									/*для верхнего уровня (называется Важно)*/
											?>
									
									
															</ul>
														</div><!--forum-block-posts-->
													</div><!--forum-block raised-corners-->
										
									
									<?
									$GLOBALS['topLevel'] = false;
									
									endif;
							endif;		
							
							__PrintForumGroupsAndForums($arRes["GROUPS"][$key], $arResult, $arParams, $depth);
							
							/*Для других уровней*/
							if(empty($arResult["GROUP"]["ID"])):
							?>
							
										
													</ul>
												</div><!--forum-block-posts-->
											</div><!--forum-block raised-corners-->
										
							
							<?
							endif;
							
						}
					}
		?>
				<?endif; /*если есть группы*/?>


				
		<?	}?><!--End function-->

		<?}?>




		<?
		if (!empty($arResult["FORUMS"])):
			if ($arResult["GROUP"]["ID"] > 0):

				__PrintForumGroupsAndForums($arResult["FORUMS"]["GROUPS"][$arResult["GROUP"]["ID"]], $arResult, $arParams, 1);
				?>
				<?if(!empty($arResult["GROUP"]["ID"])):?>
						</ul>
					</div><!--forum-block-posts-->
				</div><!--forum-block raised-corners-->
				<?endif;?>
				<?
			else:?>

				<?if(!$title):?>
										<?$this->SetViewTarget('title');?>
									   <?$title=(empty($arResult["GROUP"]["NAME"]) ? "Форум города Спутник"  : $arResult["GROUP"]["NAME"])?>
									   <noindex><h2><?=$title?></h2></noindex>
										<?$this->EndViewTarget();?> 
									<?endif?>
			<?	__PrintForumGroupsAndForums($arResult["FORUMS"], $arResult, $arParams, 0);

			endif;

			
		else:
		?>

								<div class="forum-empty-message"><?=GetMessage("F_EMPTY_FORUMS")?></div>

		<?
		endif;
		?>
				
								<!--<div class="forum-footer-inner">
		<?
				if ($arParams["SHOW_RSS"] == "Y"):
		?>
									<span class="forum-footer-option forum-footer-rss forum-footer-option-first"><noindex><?
										?><a rel="nofollow" href="<?/*=$arResult["URL"]["RSS_DEFAULT"]*/?>" onclick="window.location='<?/*=addslashes(htmlspecialcharsbx($arResult["URL"]["~RSS"]))*/?>'; return false;"><?
											?><?/*=GetMessage("F_SUBSCRIBE_TO_NEW_TOPICS")*/?><?
											?></a></noindex></span>
		<?		
				endif;
				if ($USER->IsAuthorized()):
		?>
									<span class="forum-footer-option forum-footer-markread<?/*=($arParams["SHOW_RSS"] == "Y" ? "" : " forum-footer-option-first")*/?>"><?
										?><noindex><a rel="nofollow" <?
											?>href="<?/*=$APPLICATION->GetCurPageParam("ACTION=SET_BE_READ", array("ACTION", "sessid"))*/?>" <?
											?>onclick="return this.href+=('&sessid='+BX.bitrix_sessid());";><?
											?><?/*=GetMessage("F_SET_FORUMS_READ")*/?></a></noindex></span>
		<?		
				elseif ($arParams["SHOW_RSS"] != "Y"):
		?>
									&nbsp;
		<?		
				endif;
				
		?>

		</div>-->
		
		<?

		if (!empty($arResult["NAV_STRING"]) && $arResult["NAV_RESULT"]->NavPageCount > 1):
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
		<?
		endif;
		?>

		<script>
			function selectico(el, event)
			{
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

				var formOld = $('#blockico');
				if(formOld.length > 0)
				{
					$('#icosave').unbind();
					formOld.remove(); 
				}	
				var form =
				'<div id="blockico">\
					<input id="icotxt" type="text"/>\
					<input id="icosave" type="button" value="OK"/>\
					<input id="icofile" type="file" name="icofile"/>\
					<input id="submit" type="button" value="Отправить файл"/>\
					<input id="icocancel" type="button" value="cancel" \
									onclick="icocancel();"/>\
				</div>';
				
				$(el).after(form);

				$('#icosave').bind('click', el, saveico);
				$('#submit').bind('click', el, filesubmit);
			}
			
			function saveico(el)
			{
				var id = el.data.id;
				var txt = $('#icotxt').val();
				
				BX.ajax.get('?key=icoselect&txt='+txt+'&id='+id);
				
				$('#blockico').remove();
				
				var load = '<i id="load" class="fa fa-spinner fa-spin"></i>';
				$(el.data).replaceWith(load);
			}
			
			function defaultico(el)
			{
				if($('#cancelico').length < 1)
				{
					var form = '<div id="cancelico">\
											<label>Сделать иконку по умолчанию?</label>\
											<input id="icodefok" type="button" value="OK"/>\
											<input id="icodefcancel" type="button" value="cancel" \
															onclick="icodefcancel();"/>\
										</div>';
					$(el).append(form);

					$('#icodefok').bind('click', el, icodefok);
				}
			}
			
			function icodefok(el)
			{
				var id = $(el.data).children().children()[0].id;
				var txt = 'fa fa-files-o';
				BX.ajax.get('?key=icoselect&txt='+txt+'&id='+id);
				
				$('#cancelico').remove();
				
				var load = '<i id="load" class="fa fa-spinner fa-spin"></i>';
				$($(el.data).children().children()[0]).replaceWith(load);
			}
			
			function icocancel()
			{
				var formOld = $('#blockico');
				$('#icosave').unbind();
				$('#submit').unbind();
				formOld.remove(); 
			}
			
			function icodefcancel()
			{
				var formOld = $('#cancelico');
				$('#icodefok').unbind();
				formOld.remove(); 
			}
			
			function filesubmit(el)
			{
				var con = document.getElementById('icofile');

				var f = new BX.ajax.FormData();
				f.append('key', 'fileico');
				f.append('id', el.data.id);
				f.append('file', con.files[0]);
				f.send(' ');
				
				var formOld = $('#blockico');
				formOld.remove(); 
				
				var load = '<i id="load" class="fa fa-spinner fa-spin"></i>';
				$(el.data).replaceWith(load);
			}
		</script>	
<?}?>

<?if($_GET['key'] == 'icoselect'):?>

	<script>
		var ico = '<i id="<?=$_GET['id']?>" class="<?=$_GET['txt']?>" ondblclick="selectico(this, event);"></i>'
		$('#load').replaceWith(ico);
	</script>
	
	<?
		
		 if(CModule::IncludeModule("iblock"))
		{ 
			$ibpenum = new CIBlockPropertyEnum;
			$db_res = $ibpenum->GetList(Array(), Array('IBLOCK_ID'=>IBLOCK_ID,
											'XML_ID'=>$_GET['id']));
			$arField = $db_res->Fetch();
			
			if(empty($arField['XML_ID']))
			{
				
				$PropID = $ibpenum->Add(Array('PROPERTY_ID'=>LIST_ID,  
					'XML_ID'=>$_GET['id'],'VALUE'=>$_GET['txt']));
			}
			else
			{
				if($arField['PROPERTY_CODE'] === 'PROP_IMG')
				{
					$ibpenum->Delete($arField['ID']);
					
					$PropID = $ibpenum->Add(Array('PROPERTY_ID'=>LIST_ID,  
						'XML_ID'=>$_GET['id'],'VALUE'=>$_GET['txt']));
				}
				$PropID = $ibpenum->Update($arField['ID'], Array('VALUE'=>$_GET['txt']));
			}
		}
	?>
<?endif?>

<?if($_POST['key'] == 'fileico'):?>	
	<script>
		<?if(empty($_FILES['file']['name'])):?>
			var ico = '<i id="<?=$_POST['id']?>" class="fa fa-files-o"\
								ondblclick="selectico(this, event);"></i>'
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
			console.log('<?=$IDfile?>');
			console.log('<?=$arpath['src']?>');
			var img = '<img id="<?=$_POST['id']?>" src="<?=$path['src']?>"\
								ondblclick="selectico(this, event);">';
			$('#load').replaceWith(img);
			
			<? if(CModule::IncludeModule("iblock"))
				{ 
					$ibpenum = new CIBlockPropertyEnum;
					$db_res = $ibpenum->GetList(Array(), Array('IBLOCK_ID'=>IBLOCK_ID,
													'XML_ID'=>$_POST['id']));
					$arPSField = $db_res->Fetch();
					
					if(empty($arPSField['XML_ID']))
					{
						
						$PropID = $ibpenum->Add(Array('PROPERTY_ID'=>LIST_ID_IMG,  
							'XML_ID'=>$_POST['id'],'VALUE'=>$path['src']));
					}
					else
					{
						if($arPSField['PROPERTY_CODE'] === 'PROP_ICO')
						{
							$ibpenum->Delete($arPSField['ID']);
							
							$PropID = $ibpenum->Add(Array('PROPERTY_ID'=>LIST_ID_IMG,  
								'XML_ID'=>$_POST['id'],'VALUE'=>$path['src']));
						}
						$PropID = $ibpenum->Update($arPSField['ID'], Array('VALUE'=>$path['src']));
					}
				}
			?>
			
		<?endif?>
	</script>

<?endif?>		