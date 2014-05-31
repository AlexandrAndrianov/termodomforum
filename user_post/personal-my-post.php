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
									<em class="forum-cat-prelink">
										<?if($arGroup):?>
											<a href="<?=$pathForum.'group'.$arGroup['ID']?>"><?=$arGroup['NAME']?></a>
											>	
										<?endif?>	
											<a href="<?=$pathForum.$pageTopic?>"><?=$arForum['NAME']?></a>
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
												?><a href="<?=$pathForum.$pageTopic.$arTopic['TITLE_SEO']?>" title="Тема начата <?=$arTopic["START_DATE"]?>"><?=$arTopic["TITLE"]?></a><?
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
														return false;"href="<?=$pathForum.$pageMess.'#message'.$value['idmess']?>">#<?=$value['idmess']?></a>
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