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

use Bitrix\Main\Localization\Loc;

CJSCore::Init(['amcharts4']);

?>

<input type="hidden" value="<?= $arParams['DEAL_ID'] ?>" id="pcdi_input_dealId">

<div class="ithive_prof_pcdi" id="ithive_prof_pcdi">

    <!-- region: Вывод таблицы значений -->
    <div class="ithive_prof_pcdi_table">
        <div class="ithive_prof_pcdi_table_block">
            <table class="ithive_prof_pcdi_sub_table">
                <tbody>
                <tr>
                    <th><?= Loc::getMessage('MATERIALS') ?></th>
                    <th data-logic="potential"><?= Loc::getMessage('POTENTIAL') ?></th>
                    <th data-logic="processed_tc"><?= Loc::getMessage('TC_PROCESSED') ?></th>
                    <th data-logic="processed_pm"><?= Loc::getMessage('PM_PROCESSED') ?></th>
                    <th data-logic="shipped_1c"><?= Loc::getMessage('1C_SHIPPED') ?></th>
                </tr>
                <tr>
                    <td><?= Loc::getMessage('EPPS') ?></td>
                    <td>
                        <a href="#" data-material="EPPS" data-logic="potential">
                            <?= number_format($arResult['TABLE']['UF_CONSTRUCT_MATERIAL_CONTROL'][0]['POTENTIAL'], 0, ',', ' ') ?: 0 ?>
                        </a>
                    </td>
                    <td>
                        <a href="#" data-material="EPPS" data-logic="processed_tc">
                            <?= number_format($arResult['TABLE']['UF_CONSTRUCT_MATERIAL_CONTROL'][0]['TC_PROCESSED'], 0, ',', ' ') ?: 0 ?>
                        </a>
                    </td>
                    <td>
                        <a href="#" data-material="EPPS" data-logic="processed_pm">
                            <?= number_format($arResult['TABLE']['UF_CONSTRUCT_MATERIAL_CONTROL'][0]['PM_PROCESSED'], 0, ',', ' ') ?: 0 ?>
                        </a>
                    </td>
                    <td>
                        <a href="#" data-material="EPPS" data-logic="shipped_1c">
                            <?= number_format($arResult['TABLE']['UF_CONSTRUCT_MATERIAL_CONTROL'][0]['1C_SHIPPED'], 0, ',', ' ') ?: 0 ?>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td><?= Loc::getMessage('MW') ?></td>
                    <td>
                        <a href="#" data-material="MW" data-logic="potential">
                            <?= number_format($arResult['TABLE']['UF_CONSTRUCT_MATERIAL_CONTROL'][1]['POTENTIAL'], 0, ',', ' ') ?: 0 ?>
                        </a>
                    </td>
                    <td>
                        <a href="#" data-material="MW" data-logic="processed_tc">
                            <?= number_format($arResult['TABLE']['UF_CONSTRUCT_MATERIAL_CONTROL'][1]['TC_PROCESSED'], 0, ',', ' ') ?: 0 ?>
                        </a>
                    </td>
                    <td>
                        <a href="#" data-material="MW" data-logic="processed_pm">
                            <?= number_format($arResult['TABLE']['UF_CONSTRUCT_MATERIAL_CONTROL'][1]['PM_PROCESSED'], 0, ',', ' ') ?: 0 ?>
                        </a>
                    </td>
                    <td>
                        <a href="#" data-material="MW" data-logic="shipped_1c">
                            <?= number_format($arResult['TABLE']['UF_CONSTRUCT_MATERIAL_CONTROL'][1]['1C_SHIPPED'], 0, ',', ' ') ?: 0 ?>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td><?= Loc::getMessage('PVH') ?></td>
                    <td>
                        <a href="#" data-material="PVH" data-logic="potential">
                            <?= number_format($arResult['TABLE']['UF_CONSTRUCT_MATERIAL_CONTROL'][2]['POTENTIAL'], 0, ',', ' ') ?: 0 ?>
                        </a>
                    </td>
                    <td>
                        <a href="#" data-material="PVH" data-logic="processed_tc">
                            <?= number_format($arResult['TABLE']['UF_CONSTRUCT_MATERIAL_CONTROL'][2]['TC_PROCESSED'], 0, ',', ' ') ?: 0 ?>
                        </a>
                    </td>
                    <td>
                        <a href="#" data-material="PVH" data-logic="processed_pm">
                            <?= number_format($arResult['TABLE']['UF_CONSTRUCT_MATERIAL_CONTROL'][2]['PM_PROCESSED'], 0, ',', ' ') ?: 0 ?>
                        </a>
                    </td>
                    <td>
                        <a href="#" data-material="PVH" data-logic="shipped_1c">
                            <?= number_format($arResult['TABLE']['UF_CONSTRUCT_MATERIAL_CONTROL'][2]['1C_SHIPPED'], 0, ',', ' ') ?: 0 ?>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td><?= Loc::getMessage('BITUMEN') ?></td>
                    <td>
                        <a href="#" data-material="BITUMEN" data-logic="potential">
                            <?= number_format($arResult['TABLE']['UF_CONSTRUCT_MATERIAL_CONTROL'][3]['POTENTIAL'], 0, ',', ' ') ?: 0 ?>
                        </a>
                    </td>
                    <td>
                        <a href="#" data-material="BITUMEN" data-logic="processed_tc">
                            <?= number_format($arResult['TABLE']['UF_CONSTRUCT_MATERIAL_CONTROL'][3]['TC_PROCESSED'], 0, ',', ' ') ?: 0 ?>
                        </a>
                    </td>
                    <td>
                        <a href="#" data-material="BITUMEN" data-logic="processed_pm">
                            <?= number_format($arResult['TABLE']['UF_CONSTRUCT_MATERIAL_CONTROL'][3]['PM_PROCESSED'], 0, ',', ' ') ?: 0 ?>
                        </a>
                    </td>
                    <td>
                        <a href="#" data-material="BITUMEN" data-logic="shipped_1c">
                            <?= number_format($arResult['TABLE']['UF_CONSTRUCT_MATERIAL_CONTROL'][3]['1C_SHIPPED'], 0, ',', ' ') ?: 0 ?>
                        </a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- endregion -->

    <!-- region: Нет конструктивов -->
    <?php if ($arResult['STATUS'] === 'NO_CONSTRUCTS'): ?>
        <div class="ithive_prof_pcdi_info_warning">
            <div class="ithive_prof_pcdi_warning_icon_white"></div>
            <span class="ithive_prof_pcdi_warning_text"><?= GetMessage($arResult['STATUS']) ?></span>
        </div>
    <?php endif; ?>
    <!-- endregion -->

    <!-- region: Есть конструктивы / итоговое решение не заполнено -->
    <?php if ($arResult['STATUS'] !== 'NO_CONSTRUCTS'): ?>
        <div class="ithive_prof_pcdi_info_normal" data-accordion-status="closed">

            <div class="ithive_prof_pcdi_info_left">
                <div class="ithive_prof_pcdi_warning_icon_<?= ($arResult['STATUS'] === 'ALL_FILLED') ? 'green' : 'red' ?>"></div>
            </div>

            <div class="ithive_prof_pcdi_info_right">
                <span class="ithive_prof_pcdi_normal_title"><?= GetMessage($arResult['STATUS']) ?></span>

                <table class="ithive_prof_pcdi_status_table">
                    <tbody>

                    <?php for ($i = 0; $i < 4; $i++): ?>

                        <?php
                        if (!current($arResult['TYPES'])) {
                            break;
                        }
                        ?>

                        <?php if (($i % 2) == 0): ?>
                            <tr>
                        <?php endif; ?>

                        <td>
                            <a href="javascript:void(0)" onclick="getConstructList(this)"
                               data-construct-type-id="<?= current($arResult['TYPES'])['ID'] ?>">
                                <span class="ithive_prof_pcdi_circle <?= (current($arResult['TYPES'])['TOTAL_SOLUTIONS_FILLED'] === current($arResult['TYPES'])['TOTAL_CONSTRUCTS']) ? 'green' : 'orange' ?>"></span>
                                <span class="ithive_prof_pcdi_normal_title"><?= current($arResult['TYPES'])['UF_NAME'] ?>:</span>
                                <span class="ithive_prof_pcdi_normal_text"><?= (current($arResult['TYPES'])['TOTAL_SOLUTIONS_FILLED']) ?: 0 ?>/<?= current($arResult['TYPES'])['TOTAL_CONSTRUCTS'] ?></span>
                            </a>
                        </td>

                        <?php if (($i % 2) != 0): ?>
                            </tr>
                        <?php endif; ?>

                        <?php next($arResult['TYPES']); ?>

                    <?php endfor; ?>

                    </tbody>
                </table>

                <?php if (count($arResult['TYPES']) > 4): ?>

                    <div class="ithive_prof_pcdi_hidden" style="display: none">

                        <table class="ithive_prof_pcdi_status_table" style="margin: 0">
                            <tbody>

                            <?php for ($i = 0; $i < count($arResult['TYPES']); $i++): ?>

                                <?php
                                if (!current($arResult['TYPES'])) {
                                    break;
                                }
                                ?>

                                <?php if (($i % 2) == 0): ?>
                                    <tr>
                                <?php endif; ?>

                                <td>
                                    <a href="javascript:void(0)" onclick="getConstructList(this)"
                                       data-construct-type-id="<?= current($arResult['TYPES'])['ID'] ?>">
                                        <span class="ithive_prof_pcdi_circle <?= (current($arResult['TYPES'])['TOTAL_SOLUTIONS_FILLED'] === current($arResult['TYPES'])['TOTAL_CONSTRUCTS']) ? 'green' : 'orange' ?>"></span>
                                        <span class="ithive_prof_pcdi_normal_title"><?= current($arResult['TYPES'])['UF_NAME'] ?>:</span>
                                        <span class="ithive_prof_pcdi_normal_text"><?= (current($arResult['TYPES'])['TOTAL_SOLUTIONS_FILLED']) ?: 0 ?>/<?= current($arResult['TYPES'])['TOTAL_CONSTRUCTS'] ?></span>
                                    </a>
                                </td>

                                <?php if (($i % 2) != 0): ?>
                                    </tr>
                                <?php endif; ?>

                                <?php next($arResult['TYPES']); ?>

                            <?php endfor; ?>

                            </tbody>
                        </table>

                    </div>

                    <a href="#" class="ithive_prof_pcdi_moreless" onclick="resizeInfoContainer(this)">
                        <span>Показать больше</span>
                        <div class="ithive_prof_pcdi_down"></div>
                    </a>

                <?php endif; ?>

            </div>

        </div>
    <?php endif; ?>
    <!-- endregion -->

</div>

<script>

    BX.ready(function () {

        /*
        * Если есть в карточке сделки пользовательские поля таблиц, удаляем их из DOM
        */
        let ufThermalTableNode = document.querySelector('div[data-cid="UF_THERMAL_TABLE"]');
        if (ufThermalTableNode) {
            ufThermalTableNode.remove();
        }
        let ufHydroTableNode = document.querySelector('div[data-cid="UF_HYDRO_TABLE"]');
        if (ufHydroTableNode) {
            ufHydroTableNode.remove();
        }
        let ufConstructMaterialControlTableNode = document.querySelector('div[data-cid="UF_CONSTRUCT_MATERIAL_CONTROL"]');
        if (ufConstructMaterialControlTableNode) {
            ufConstructMaterialControlTableNode.remove();
        }
        BX.addCustomEvent('bx.ui.entityeditorfield:onlayout', BX.delegate(function (selectorName) {
            if (selectorName._draggableContextId === 'editor_field') {
                ufThermalTableNode = document.querySelector('div[data-cid="UF_THERMAL_TABLE"]');
                if (ufThermalTableNode) {
                    ufThermalTableNode.remove();
                }
                ufHydroTableNode = document.querySelector('div[data-cid="UF_HYDRO_TABLE"]');
                if (ufHydroTableNode) {
                    ufHydroTableNode.remove();
                }
                ufConstructMaterialControlTableNode = document.querySelector('div[data-cid="UF_CONSTRUCT_MATERIAL_CONTROL"]');
                if (ufConstructMaterialControlTableNode) {
                    ufConstructMaterialControlTableNode.remove();
                }
            }
        }));

        /*
        * Добавляем обработчики нажатия на ссылки чисел по материалам в таблице
        */
        let aNodeList = document.querySelectorAll('table.ithive_prof_pcdi_sub_table > tbody > tr > td > a');
        aNodeList.forEach(function (data, index) {
            data.addEventListener(
                'click',
                function () {
                    getDataForChartPopup(event)
                },
                false
            );
        });

    });

    /**
     * Меняем размер информационного окна в зависимости от размера его контента
     *
     * @param node
     */
    function resizeInfoContainer(node) {
        let pcdiContainerInfoNode = document.querySelector('div.ithive_prof_pcdi_info_normal');
        let accordionStatus = pcdiContainerInfoNode.dataset.accordionStatus;
        let angleNode = node.querySelector('div');
        let text = node.querySelector('span');
        let hiddenText = pcdiContainerInfoNode.querySelector('div.ithive_prof_pcdi_hidden');

        switch (accordionStatus) {
            case 'opened':
                hiddenText.style.display = 'none';
                pcdiContainerInfoNode.style.height = '140px';
                pcdiContainerInfoNode.setAttribute('data-accordion-status', 'closed');
                angleNode.classList.remove('ithive_prof_pcdi_up');
                angleNode.classList.add('ithive_prof_pcdi_down');
                pcdiContainerInfoNode.classList.remove('ithive_prof_pcdi_box_shadow');
                text.innerText = 'Показать больше';
                break;
            case 'closed':
                hiddenText.style.display = 'block';
                pcdiContainerInfoNode.style.height = (hiddenText.offsetHeight - 40) + pcdiContainerInfoNode.offsetHeight + 'px';
                pcdiContainerInfoNode.setAttribute('data-accordion-status', 'opened');
                angleNode.classList.remove('ithive_prof_pcdi_down');
                angleNode.classList.add('ithive_prof_pcdi_up');
                pcdiContainerInfoNode.classList.add('ithive_prof_pcdi_box_shadow');
                text.innerText = 'Скрыть';
                break;
        }
    }

    /**
     * Эмулируем нажатие на таб "Конструктивы"
     *
     * onclick="goToConstructList(this)"
     *
     * @param node
     */
    function goToConstructList(node) {

        /*
        В data нужно передать id типа конструкций - при нажатии ждать открытия слайдера и fireEvent на том menu item,
        на который прежде нажали
         */

        let constructListTabNode = document.querySelector('[data-tab-id="construct-list"]');
        BX.fireEvent(
            constructListTabNode,
            'click'
        );
    }

    /**
     * По клику на ссылки типов конструктивов в правом информационном блоке в сделке,
     * перенаправляет пользователя на страницу списка конструктивов и выбирает в меню тип на который пользователь кликнул
     *
     * @param node
     */
    function getConstructList(node) {
        let tabNode = document.querySelector('div[data-tab-id="construct-list"]');
        if (tabNode) {
            BX.fireEvent(
                document.querySelector('div[data-tab-id="construct-list"]'),
                'click'
            );
            let menu;
            let timerId = setTimeout(function () {
                menu = BX('ithive_prof_pcs_menu');
                if (menu) {
                    clearInterval(timerId);
                    let menuItems = menu.getElementsByClassName('ithive-prof-pcs-menu-item');
                    for (let i = 0; i < menuItems.length; i++) {
                        if (menuItems[i].dataset.constructTypeId === node.dataset.constructTypeId) {
                            window.BX.Ithive.Prof.Pcs.selectMenuItem(menuItems[i], false);
                            break;
                        }
                    }
                }
            }, 100);
        }
    }

    /**
     * Получаем данные для отображения на графике amCharts
     *
     * @param event
     */
    function getDataForChartPopup(event) {
        event.preventDefault();
        BX.ajax.runComponentAction(
            'ithive:prof.construct.deal.info',
            'getDataChart',
            {
                mode: 'class',
                data: {
                    dealId: document.getElementById('pcdi_input_dealId').value,
                    material: event.target.dataset.material,
                    logic: event.target.dataset.logic
                }
            },
        ).then(function (response) {
            if (response.status === 'success') {
                showChartPopup(event, response.data);
            } else {
                console.error('error has been occurred!');
            }
        });
    }

    /**
     * Отображаем popup с графиком amCharts
     *
     * @param event
     * @param data
     */
    function showChartPopup(event, data) {

        // Получаем заголовок для popup
        let popupTitlePart1 = event.target.parentNode.parentNode.children[0].innerText;
        let popupTitlePart2 = event.target.parentNode.parentNode.parentNode.querySelector('th[data-logic="' + event.target.dataset.logic + '"]').innerText;

        // Создаем контент для popup
        let content = `
        <div class="ithive_prof_pcdi__popup_header">
            <span class="ithive_prof_pcdi__popup_header_title_1">${popupTitlePart1}</span>
            <span class="ithive_prof_pcdi__popup_header_title_2">${popupTitlePart2}</span>
        </div>
        <div class="ithive_prof_pcdi__popup_delimiter"></div>
        <div class="ithive_prof_pcdi__popup_chart_base">
            <div id="control_materials_popup_amcharts"></div>
        </div>
        `;

        // Создаем popup
        let popup = BX.PopupWindowManager.create('control_materials_popup', BX('element'), {
            content: content,
            width: 640, // ширина окна
            height: 440, // высота окна
            zIndex: 100, // z-index
            closeIcon: {
                height: '50px',
                width: '50px'
            },
            titleBar: '',
            closeByEsc: true, // закрытие окна по esc
            darkMode: false, // окно будет светлым или темным
            autoHide: false, // закрытие при клике вне окна
            draggable: true, // можно двигать или нет
            resizable: true, // можно ресайзить
            min_height: 200, // минимальная высота окна
            min_width: 200, // минимальная ширина окна
            lightShadow: true, // использовать светлую тень у окна
            angle: false, // появится уголок
            overlay: {
                // объект со стилями фона
                backgroundColor: 'black',
                opacity: 500
            },
            buttons: [
                new BX.PopupWindowButton({
                    text: '<?=Loc::getMessage("CLOSE")?>',
                    id: 'control_materials_popup__close_button',
                    className: 'ui-btn ui-btn-primary',
                    events: {
                        click: function () {
                            // Событие при клике на кнопку
                            popup.destroy();
                        }
                    }
                })
            ],
            events: {
                onPopupShow: function () {
                    // Событие при показе окна
                    /**/
                },
                onPopupClose: function () {
                    // Событие при закрытии окна
                    popup.destroy();
                }
            }
        });
        popup.show();

        // amCharts
        let chart = am4core.create('control_materials_popup_amcharts', am4charts.PieChart);
        let colorList = [
            '#3D4B5C',
            '#0091EA',
            '#8FBB56',
            '#FCBB5A',
            '#0DB7AE',
            '#B82C2C',
            '#64D7F8',
            '#EDCE28',
            '#9900CC',
            '#E36729',
            '#0033CC',
            '#996633',
        ];
        let chartData = [];
        let i = 0;
        for (let key in data)
        {
            chartData.push({
                UF_NAME: data[key].UF_NAME,
                TOTAL: data[key].TOTAL,
                color: am4core.color(colorList[i]),
                text:'1',
                icon: 'red'
            });
            i++;
            if (i === colorList.length) {
                i = 0;
            }
        }
        chart.data = chartData;
        let pieSeries = chart.series.push(new am4charts.PieSeries({}));
        pieSeries.dataFields.value = 'TOTAL';
        pieSeries.dataFields.category = 'UF_NAME';
        pieSeries.slices.template.propertyFields.fill = 'color';
        pieSeries.slices.template.tooltipText = '{TOTAL}';
        pieSeries.labels.template.fontSize = 12;

        //chart.radius = am4core.percent(100);

        //pieSeries.labels.template.text = 'new text';

        //chart.legend = new am4charts.Legend();
        //chart.legend.position = 'left';

        let rect = pieSeries.labels.template.text.createChild(am4core.Rectangle);
        rect.width = 20;
        rect.height = 20;
        rect.align = "center";
        rect.valign = "middle";
        rect.strokeWidth = 2;
        rect.fill = am4core.color('blue');
        rect.stroke = am4core.color('yellow');

    }

</script>

<style>

    /* Общее */
    .ithive_prof_pcdi {
        margin-top: 20px;
        flex-wrap: nowrap;
        display: flex;
        font-family: Open Sans, serif;
        font-style: normal;
        max-height: 180px;
        min-height: 180px;
        font-size: 12px;
    }

    .ithive_prof_pcdi_box_shadow {
        -webkit-box-shadow: 0 0 8px 0 rgba(34, 60, 80, 0.2);
        -moz-box-shadow: 0 0 8px 0 rgba(34, 60, 80, 0.2);
        box-shadow: 0 0 8px 0 rgba(34, 60, 80, 0.2);
    }

    /* Таблица */
    .ithive_prof_pcdi_table {
        background: white;
        flex-wrap: nowrap;
        /*display: flex;*/
        min-width: 60%;
        max-width: 60%;
        justify-content: space-between;
        overflow: auto;
    }

    .ithive_prof_pcdi_sub_table {
        border-collapse: collapse;
        margin-top: 14px;
        color: #535C69;
        width: 100%;
    }

    .ithive_prof_pcdi_sub_table > tbody > tr > td {
        min-width: max-content;
        border: 1px solid #E5E5E5;
        padding: 5px;
    }

    .ithive_prof_pcdi_sub_table > tbody > tr > td:hover:not(:first-child) {
        background: #EEF2F4;
    }

    .ithive_prof_pcdi_sub_table > tbody > tr > th {
        /*border: 1px solid #E5E5E5;*/
        padding: 5px 5px 5px 0;
    }

    .ithive_prof_pcdi_sub_table > tbody > tr > th:first-child {
        /*border: 1px solid #E5E5E5;*/
        width: 50%;
    }

    .ithive_prof_pcdi_sub_table > tbody > tr > th:not(:first-child) {
        /*border: 1px solid #E5E5E5;*/
        width: 12.5%;
    }

    .ithive_prof_pcdi_delimiter {
        border-left: 1px solid #E5E5E5;
        margin: 20px 0 20px 0;
    }

    .ithive_prof_pcdi_table_title {
        color: #333333;
        font-weight: 600;
        font-size: 15px;
        line-height: 20px;
    }

    .ithive_prof_pcdi_table_block {
        min-width: max-content;
        flex-wrap: wrap;
        padding: 0 20px 0 20px;
    }

    /* Инфо - нет конструктивов */
    .ithive_prof_pcdi_info_warning {
        width: 100%;
        background: #FD9899;
        padding: 20px;
        margin-left: 20px;
        flex-direction: row;
        display: flex;
        align-items: start;
    }

    .ithive_prof_pcdi_warning_text {
        color: #FFFFFF;
        font-size: 14px;
        line-height: 20px;
    }

    .ithive_prof_pcdi_warning_icon_white {
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg width='23' height='23' viewBox='0 0 23 23' fill='none' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M11.5 20.5C13.8869 20.5 16.1761 19.5518 17.864 17.864C19.5518 16.1761 20.5 13.8869 20.5 11.5C20.5 9.11305 19.5518 6.82387 17.864 5.13604C16.1761 3.44821 13.8869 2.5 11.5 2.5C9.11305 2.5 6.82386 3.44821 5.13604 5.13604C3.44821 6.82387 2.5 9.11305 2.5 11.5C2.5 13.8869 3.44821 16.1761 5.13604 17.864C6.82386 19.5518 9.11305 20.5 11.5 20.5ZM11.5 22.75C5.28662 22.75 0.25 17.7134 0.25 11.5C0.25 5.28663 5.28662 0.25 11.5 0.25C17.7134 0.25 22.75 5.28663 22.75 11.5C22.75 17.7134 17.7134 22.75 11.5 22.75ZM10.375 16H12.625V18.25H10.375V16ZM10.375 4.75H12.625V13.75H10.375V4.75Z' fill='white'/%3e%3c/svg%3e");
        width: 23px;
        height: 23px;
        margin-right: 20px;
    }

    /* Инфо - есть конструктивы (нет итоговых решений) */
    .ithive_prof_pcdi_info_normal {
        width: 100%;
        background: #FFFFFF;
        padding: 20px;
        margin-left: 20px;
        display: flex;
        flex-wrap: nowrap;
        z-index: 200;
        font-size: 12px;
    }

    .ithive_prof_pcdi_warning_icon_red {
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg width='23' height='23' viewBox='0 0 23 23' fill='none' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M11.5 20.5C13.8869 20.5 16.1761 19.5518 17.864 17.864C19.5518 16.1761 20.5 13.8869 20.5 11.5C20.5 9.11305 19.5518 6.82387 17.864 5.13604C16.1761 3.44821 13.8869 2.5 11.5 2.5C9.11305 2.5 6.82386 3.44821 5.13604 5.13604C3.44821 6.82387 2.5 9.11305 2.5 11.5C2.5 13.8869 3.44821 16.1761 5.13604 17.864C6.82386 19.5518 9.11305 20.5 11.5 20.5ZM11.5 22.75C5.28662 22.75 0.25 17.7134 0.25 11.5C0.25 5.28663 5.28662 0.25 11.5 0.25C17.7134 0.25 22.75 5.28663 22.75 11.5C22.75 17.7134 17.7134 22.75 11.5 22.75ZM10.375 16H12.625V18.25H10.375V16ZM10.375 4.75H12.625V13.75H10.375V4.75Z' fill='%23FF9900'/%3e%3c/svg%3e");
        width: 23px;
        height: 23px;
    }

    .ithive_prof_pcdi_info_left {
        width: 23px;
    }

    .ithive_prof_pcdi_info_right {
        width: 100%;
        margin-left: 20px;
        position: relative;
    }

    .ithive_prof_pcdi_normal_title {
        color: #333333;
        /*font-size: 14px;*/
        font-weight: 600;
    }

    .ithive_prof_pcdi_no_margin {
        margin: 0;
    }

    .ithive_prof_pcdi_moreless {
        font-weight: 600;
        position: absolute;
        bottom: 0;
        display: flex;
        flex-direction: row;
        align-items: start;
    }

    .ithive_prof_pcdi_down {
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg width='10' height='6' viewBox='0 0 10 6' fill='none' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M1.49961 0.200012L4.99961 3.70001L8.49961 0.200012L9.89961 0.900012L4.99961 5.80001L0.0996094 0.900012L1.49961 0.200012Z' fill='%23535C69'/%3e%3c/svg%3e");
        width: 10px;
        height: 6px;
        margin: 8px 0 0 12px;
    }

    .ithive_prof_pcdi_up {
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg width='10' height='6' viewBox='0 0 10 6' fill='none' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M1.49961 5.79999L4.99961 2.29999L8.49961 5.79999L9.89961 5.09999L4.99961 0.199988L0.0996094 5.09999L1.49961 5.79999Z' fill='%23535C69'/%3e%3c/svg%3e");
        width: 10px;
        height: 6px;
        margin: 8px 0 0 12px;
    }

    /* Инфо - есть конструктивы (итоговые решения не заполнены) */
    .ithive_prof_pcdi_status_table {
        width: 100%;
        margin-top: 10px;
    }

    .ithive_prof_pcdi_status_table > tbody > tr > td {
        padding: 10px;
    }

    .ithive_prof_pcdi_normal_text {
        font-weight: 400;
    }

    .ithive_prof_pcdi_circle {
        height: 12px;
        width: 12px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
    }

    .orange {
        background: #FCBB5A;
    }

    .green {
        background: #77BC1F;
    }

    /* Инфо - есть конструктивы (итоговые решения заполнены) */
    .ithive_prof_pcdi_warning_icon_green {
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg width='23' height='23' viewBox='0 0 23 23' fill='none' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M11.5 20.5C13.8869 20.5 16.1761 19.5518 17.864 17.864C19.5518 16.1761 20.5 13.8869 20.5 11.5C20.5 9.11305 19.5518 6.82387 17.864 5.13604C16.1761 3.44821 13.8869 2.5 11.5 2.5C9.11305 2.5 6.82386 3.44821 5.13604 5.13604C3.44821 6.82387 2.5 9.11305 2.5 11.5C2.5 13.8869 3.44821 16.1761 5.13604 17.864C6.82386 19.5518 9.11305 20.5 11.5 20.5ZM11.5 22.75C5.28662 22.75 0.25 17.7134 0.25 11.5C0.25 5.28663 5.28662 0.25 11.5 0.25C17.7134 0.25 22.75 5.28663 22.75 11.5C22.75 17.7134 17.7134 22.75 11.5 22.75ZM10.375 16H12.625V18.25H10.375V16ZM10.375 4.75H12.625V13.75H10.375V4.75Z' fill='%2377BC1F'/%3e%3c/svg%3e");
        width: 23px;
        height: 23px;
    }

    /* Popup */
    #control_materials_popup {
        border-radius: 10px;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.15);
    }

    .ithive_prof_pcdi__popup_header {
        width: max-content;
        margin: 0 10px 0 10px;
        color: #535C69;
        font-family: "Open Sans";
        font-size: 14px;
        line-height: 30px;
        letter-spacing: .66px;
    }

    .ithive_prof_pcdi__popup_header_title_1 {
        font-weight: 600;
    }

    .ithive_prof_pcdi__popup_header_title_2 {
        font-weight: 400;
        margin: 0 0 0 10px;
    }

    .ithive_prof_pcdi__popup_delimiter {
        background: #EEF2F4;
        margin: 10px 10px 0 10px;
        height: 1px;
        width: available;
    }

    .ithive_prof_pcdi__popup_chart_base {
        margin: 10px 10px 0 10px;
        width: available;
        height: 300px;
        background: #EEF2F4;
        border-radius: 4px;
    }

    #control_materials_popup__close_button {
        border: none;
        color: #535C69;
        font-family: 'Open Sans';
        font-style: normal;
        font-weight: 700;
        font-size: 13px;
        line-height: 18px;
        background: none;
        letter-spacing: .66px;
    }

    #control_materials_popup__close_button:hover {
        background: #EEF2F4;
    }

    #control_materials_popup_amcharts {
        top: 8%;
        height: 250px !important;
    }


</style>
