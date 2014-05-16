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
                    	<h2>Профиль</h2>
                        <div class="user-avatar-and-name clear">
                          
                            <img src="<?=$URLimg?>"/>
                              <div class="infos">
                                <h4><? if ($arUser['NAME']!="" || $arUser['SECOND_NAME']!="" || $arUser['SECOND_NAME']!=""):?><?=$arUser['NAME']?> <?=$arUser['SECOND_NAME']?> <?=$arUser['LAST_NAME']?><?else:?><?=$arUser['LOGIN']?><?endif;?></h4>
                                <!--i>Major</i-->
                                <ul>
                                    <!--li>
                                        <em>Сообщений:</em>
                                        <span>550</span>
                                    </li-->
                                    <li>
                                        <em>Регистрация:</em>               
                                        <span><?=$arUser['DATE_REGISTER']?></span>
                                    </li>
                                    <li>
                                        <em>Последний визит:</em>
                                        <span><?=$arUser['TIMESTAMP_X']?></span>
                                    </li>
                                </ul>  
                             <?
                          
                            if(!$isIm):?>
                                      <!--a href="#" class="btn green" data-toggle="modal" data-target="#modalform"><i class="fa fa-envelope"></i> Написать сообщение пользователю</a--> 
                               <? endif; ?>
                            </div>
                        </div>
                        <??>
                        <? if ($arUser['PERSONAL_BIRTHDAY']!="" || $arUser['PERSONAL_PROFESSION']!="" || $arUser['PERSONAL_NOTES']!="" || $arUser['PERSONAL_CITY']!="" || $arUser['PERSONAL_WWW']!=""):?>
                        <div class="personal-page-container">
                            <div class="personal-page-block-container">
                                <div class="personal-page-block raised-corners">
                                    <h3>Личные данные</h3>
                                      <table class="inputs-table">
                                        <?if ($arUser['PERSONAL_BIRTHDAY']):?><tr>
                                            <td><span>Дата рождения</span></td>
                                            <td>
                                                <span><?=$arUser['PERSONAL_BIRTHDAY']?></span>
                                            </td>
                                        </tr><?endif;?>
                                        <?if ($arUser['PERSONAL_PROFESSION']):?><tr>
                                            <td><span>Профессия</span></td>
                                            <td>
                                                <span><?=$arUser['PERSONAL_PROFESSION']?></span>
                                            </td>
                                        </tr><?endif;?>
                                        <?if ($arUser['PERSONAL_CITY']):?><tr>
                                            <td><span>Город</span></td>
                                            <td>
                                                <span><?=$arUser['PERSONAL_CITY']?></span>
                                            </td>
                                        </tr><?endif;?>
                                        <?if ($arUser['PERSONAL_WWW']):?><tr>
                                            <td><span>Сайт</span></td>
                                            <td>
                                                <span><noindex><?=$arUser['PERSONAL_WWW']?></noindex></span>
                                            </td>
                                        </tr><?endif;?>
                                        <?if ($arUser['PERSONAL_NOTES']):?><tr>
                                            <td><span>Интересы</span></td>
                                            <td>
                                                <span><?=$arUser['PERSONAL_NOTES']?></span>
                                            </td>
                                        </tr><?endif;?>
                                        
                                       <? if($isIm) :?>
                                        <tr class="divider"><td colspan="2"><a href="/personal/" class="btn green">Изменить информацию</a></td></tr>
                                       <?endif;?>
                                      </table>
                                </div>        
                                    
                            </div>

                            <div class="clear"></div>
                        </div>
                        <? endif;?>
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