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

$APPLICATION->ShowHead();

use Bitrix\Main\UI\Extension;

Extension::load(['ui.forms', 'ui.buttons.icons', 'ui.entity-selector', 'ui.vue', 'sidepanel']);

?>

<div id="prof_construct_list_solution_edit_vue">

    <div class="ithive-prof-pclpe">

        <h6 class="ithive-prof-pclpe-main-title"><?= ($arParams['solutionType'] === 'TOTAL_SOLUTION') ? GetMessage('TITLE_TOTAL_SOLUTION') : GetMessage('TITLE_ACTUAL_SALE') ?></h6>

        <div class="ithive-prof-pclpe-main-content">

            <div class="ithive-prof-pclpe-sub-content">

                <!-- разный title в зависимости от типа того что добавляем -->
                <input-radio
                        label="<?= GetMessage('TRANSFER_VALUES') ?>"
                        logic="<?= $arParams['solutionType']?>"
                ></input-radio>

            </div>

        </div>

    </div>

    <!-- SUB MENU -->
    <sub-menu></sub-menu>

</div>

<script>

    BX.Vue.component('input-radio', {
        template: '' +
            '<div>' +
            '<label class="ithive-prof-pclpe-input-label">{{label}}</label>' +
            '<br>' +
            '' +
            '<div v-if="this.logic === \'TOTAL_SOLUTION\' && \'RE_APPROVAL\' === \'<?=$arParams['workTypeCode']?>\'">' +
            '<label>' +
            '<input type="radio" class="hide" name="fa-check-circle" value="PROJECT_SOLUTION" v-model="getValue">' +
            '<i class="fa fa-fw fa-check-circle"></i>' +
            '<span class="ithive-prof-pclpe-input-checkbox-text"><?=GetMessage('DO_FROM_PROJECT_SOLUTION')?></span>' +
            '</label>' +
            '<br>' +
            '</div>' +
            '' +
            '<div v-if="this.logic === \'TOTAL_SOLUTION\'">' +
            '<label>' +
            '<input type="radio" class="hide" name="fa-check-circle" value="OUR_OFFER" v-model="getValue">' +
            '<i class="fa fa-fw fa-check-circle"></i>' +
            '<span class="ithive-prof-pclpe-input-checkbox-text"><?=GetMessage('DO_FROM_OUR_OFFER')?></span>' +
            '</label>' +
            '<br>' +
            '</div>' +
            '' +
            '<div v-if="this.value == \'OUR_OFFER\'">' + // Поддержка выбора любого "Нашего предложения" для переноса значений
            '<input-select ' +
            'label=\'<?=GetMessage("SELECT_OUR_OFFER")?>\' ' +
            '></input-select>' +
            '</div>' +
            '' +
            '<div v-if="this.logic === \'ACTUAL_SALE\'">' +
            '<label>' +
            '<input type="radio" class="hide" name="fa-check-circle" value="TOTAL_SOLUTION" v-model="getValue">' +
            '<i class="fa fa-fw fa-check-circle"></i>' +
            '<span class="ithive-prof-pclpe-input-checkbox-text"><?=GetMessage('DO_FROM_TOTAL_SOLUTION')?></span>' +
            '</label>' +
            '<br>' +
            '</div>' +
            '' +
            '<label>' +
            '<input type="radio" class="hide" name="fa-check-circle" value="SELF" v-model="getValue">' +
            '<i class="fa fa-fw fa-check-circle"></i>' +
            '<span class="ithive-prof-pclpe-input-checkbox-text">Заполнить самостоятельно</span>' +
            '</label>' +
            '</div>' +
            '',
        props: [
            'label',
            'value',
            'logic',
        ],
        computed: {
            getValue: {
                get() {
                    return this.value;
                },
                set(value) {
                    this.value = value;
                    this.$emit('input', value);
                    this.$root.preMaterial.selectedMode = value;
                }
            }
        },
    });

    BX.Vue.component('input-select', {
        template: '' +
            '<div class="ithive-prof-pclpe-input-box">' +
            '<label class="ithive-prof-pclpe-input-label">{{label}}</label>' +
            '<div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown ithive-prof-pclpe-input-field">' +
            '<div class="ui-ctl-after ui-ctl-icon-angle"></div>' +
            '<select class="ui-ctl-element" v-model="getValue" :disabled="disabled">' +
            '<option v-for="option in options" :value="option.id">{{option.title}}</option>' +
            '</select>' +
            '</div>' +
            '</div>' +
            '',
        props: [
            'label',
            'options',
            'value',
            'disabled',
        ],
        computed: {
            getValue: {
                get() {
                    return this.value;
                },
                set(value) {
                    this.value = value;
                    this.$emit('input', value);
                    switch (this.$root.preMaterial.selectedMode)
                    {
                        case 'OUR_OFFER':
                            this.$root.preMaterial.solution.ourOffer = value;
                            break;
                    }
                }
            }
        },
        mounted() {
            let root = this.$root;
            switch (root.preMaterial.selectedMode)
            {
                case 'OUR_OFFER':
                    this.options = this.$root.preMaterial.list.ourOffer;
                    break;
            }
        },
        destroyed() {
            let root = this.$root;
            root.preMaterial.solution.ourOffer = 0;
        }

    });

    BX.Vue.component('sub-menu', {
        template: '' +
            '<div class="ithive-prof-pclpe-sub-menu">' +
            '<div class="ithive-prof-pclpe-row">' +
            '<button class="ui-btn ui-btn-primary ithive-prof-pcl-standard-button" @click="save"><?= GetMessage('NEXT') ?></button>' +
            '<a href="#" class="ui-btn ui-btn-link" @click="close"><?= GetMessage('CANCEL') ?></a>' +
            '</div>' +
            '</div>' +
            '',
        methods: {
            close() {
                this.$root.closeSlider();
            },
            save() {
                this.$root.goNext();
            },
        },
    });

    var application = BX.Vue.create({
        el: '#prof_construct_list_solution_edit_vue',
        data() {
            return {
                component: '<?=$this->getComponent()->getName()?>',
                error: 'error has been occurred - ajax action: ',
                slider: null,
                preMaterial: {
                    solution: {
                        projectSolution: <?=$arParams['projectSolutionId']?:0?>,
                        ourOffer: 0,
                        totalSolution: <?=$arParams['totalSolutionId']?:0?>,
                    },
                    list: {
                        projectSolution: [], // на данный момент не используется
                        ourOffer: [],
                        totalSolution: [],   // на данный момент не используется
                    },
                    currentSolutionType: '<?=$arParams['solutionType']?>',
                    selectedMode: '',
                    construct: <?=$arParams['constructId']?>,
                    dealStage: '<?=$arParams['dealStage']?>',
                    subConstructId: 0,
                },
            };
        },
        methods: {
            goNext() {
                if (!this.validateData()) {
                    return;
                }
                let root = this;
                let action = 'createSolution';

                BX.ajax.runComponentAction(
                    this.component,
                    action,
                    {
                        mode: 'class',
                        data: {
                            preMaterial: root.preMaterial,
                        },
                    },
                ).then(function (response) {
                    if (response.status === 'success') {
                        root.slider.data.data.status = 'save';
                        root.slider.data.data.constructId = <?=$arParams['constructId']?>;
                        root.slider.data.data.subConstructId = response.data.subType;
                        root.slider.data.data.constructType = '<?=$arParams['constructType']?>';
                        root.slider.data.data.solutionType = '<?=$arParams['solutionType']?>';
                        root.slider.data.data.responsibleProject = <?=$USER->GetID()?>;
                        root.slider.data.data.solutionId = response.data.newSolutionId;
                        root.slider.data.data.systemId = response.data.systemId;
                        root.slider.data.data.selectedMode = root.preMaterial.selectedMode;
                        root.closeSlider();
                    } else {
                        console.error(root.error + action);
                    }
                });
            },
            getOurOffers() {
                let root = this;
                let action = 'getOurOffers';

                BX.ajax.runComponentAction(
                    this.component,
                    action,
                    {
                        mode: 'class',
                        data: {
                            constructId: root.preMaterial.construct,
                        },
                    },
                ).then(function (response) {
                    if (response.status === 'success') {
                        root.preMaterial.list.ourOffer = response.data;
                    } else {
                        console.error(root.error + action);
                    }
                });
            },
            getSlider() {
                let root = this;
                BX.SidePanel.Instance.openSliders.forEach(function(data, index){
                    if (data.url === root.component) {
                        root.slider = BX.SidePanel.Instance.openSliders[index];
                    }
                });
            },
            closeSlider() {
                if (this.slider.data.data.status !== 'save')
                {
                    this.slider.data.data.status = 'cancel'
                }
                this.slider.close();
            },
            validateData() {

                // Проверяем данные перед сохранением
                if (this.preMaterial.selectedMode === '')
                {
                    alert('<?=GetMessage('ALERT_SELECT_MODE')?>');
                    return false;
                }

                switch (this.preMaterial.selectedMode)
                {
                    case 'OUR_OFFER':
                        if (this.preMaterial.solution.ourOffer === 0) {
                            alert('<?=GetMessage('ALERT_SELECT_OUR_OFFER')?>');
                            return false;
                        }
                        break;
                }

                return true;
            }
        },
        mounted() {
            this.getSlider();
            this.getOurOffers();
        },
    })
</script>
