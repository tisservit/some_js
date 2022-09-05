<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

if ($arParams['responsibleProject'] > 0) {
    $rsUser = CUser::GetList([], [], ['ID' => $arParams['responsibleProject']]);
    if ($user = $rsUser->Fetch())
    {
        $arResult['RESPONSIBLE_PROJECT_FULL_NAME'] = CUser::FormatName(CSite::GetNameFormat(), $user, true, false);
    }
} else {
    $rsUser = CUser::GetList([], [], ['ID' => $USER->GetID()]);
    if ($user = $rsUser->Fetch())
    {
        $arResult['RESPONSIBLE_MATERIAL_FULL_NAME'] = CUser::FormatName(CSite::GetNameFormat(), $user, true, false);
    }
}
