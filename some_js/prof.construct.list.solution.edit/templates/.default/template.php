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

    <div class="ithive-prof-pclse">

        <h6 class="ithive-prof-pclse-main-title"><?= GetMessage('TITLE') ?></h6>

        <div class="ithive-prof-pclse-main-content">

            <input-user
                    label="<?= GetMessage('RESPONSIBLE') ?>"
                    v-model="solution.responsible"
                    v-if="this.solution.type !== 'ACTUAL_SALE'"
            ></input-user>

            <input-select
                    label="<?= GetMessage('CONSTRUCT_SUB_TYPE') ?>"
                    v-model="solution.subType"
                    :options="list.subType"
            ></input-select>

        </div>

    </div>

    <!-- SUB MENU -->
    <sub-menu></sub-menu>

</div>

<script>

    BX.Vue.component('input-user', {
        template: '' +
            '<div class="ithive-prof-pclse-input-box">' +
            '<label class="ithive-prof-pclse-input-label">{{label}}</label>' +
            '<div style="margin-top: 5px">' +
            '</div>' +
            '</div>' +
            '',
        props: [
            'label',
        ],
        data() {
            return {
                additionalData: {
                    isFirstShow: true,
                }
            }
        },
        mounted() {
            this.$el.children[1].setAttribute('id', 'input-user-' + this._uid);
            let root = this.$root;
            let tagSelector = new BX.UI.EntitySelector.TagSelector({
                multiple: false,
                items: [
                    {
                        id: root.solution.responsible,
                        entityId: 'U' + root.solution.responsible,
                        title: root.solution.responsibleFullName,
                    }
                ],
                dialogOptions: {
                    multiple: false,
                    cacheable: true,
                    context: 'userSelect',
                    entities: [
                        {
                            id: 'department', // структура компании: выбор только пользователей
                        },
                    ],
                },
                events: {
                    onAfterTagAdd: (event) => {
                        root.solution.responsible = event.data.tag.id;
                    },
                    onTagRemove: (event) => {
                        root.solution.responsible = null;
                    }
                }
            });
            tagSelector.renderTo(document.getElementById('input-user-' + this._uid));
        },
    });

    BX.Vue.component('input-select', {
        template: '' +
            '<div class="ithive-prof-pclse-input-box">' +
            '<label class="ithive-prof-pclse-input-label">{{label}}</label>' +
            '<div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown ithive-prof-pclse-input-field">' +
            '<div class="ui-ctl-after ui-ctl-icon-angle"></div>' +
            '<select class="ui-ctl-element" v-model="getValue">' +
            '<option v-for="option in options" :value="option.id">{{option.title}}</option>' +
            '</select>' +
            '</div>' +
            '</div>' +
            '',
        props: [
            'label',
            'options',
            'value',
        ],
        computed: {
            getValue: {
                get() {
                    return this.value;
                },
                set(value) {
                    this.$emit('input', value);
                }
            }
        },
    });

    BX.Vue.component('sub-menu', {
        template: '' +
            '<div class="ithive-prof-pclse-sub-menu">' +
            '<div class="ithive-prof-pclse-row">' +
            '<button class="ui-btn ui-btn-primary ithive-prof-pcl-standard-button" @click="save"><?= GetMessage('SAVE') ?></button>' +
            '<a href="#" class="ui-btn ui-btn-link" @click="close"><?= GetMessage('CANCEL') ?></a>' +
            '</div>' +
            '</div>' +
            '',
        methods: {
            close() {
                this.$root.closeSlider();
            },
            save() {
                this.$root.saveSolution();
            },
        }
    });

    var application = BX.Vue.create({
        el: '#prof_construct_list_solution_edit_vue',
        data() {
            return {
                component: '<?=$this->getComponent()->getName()?>',
                error: 'error has been occurred - ajax action: ',
                slider: null,
                solution: {
                    id: <?=$arParams['projectSolutionId']?:0?>,
                    construct: <?=$arParams['constructId']?>,
                    type: '<?=$arParams['solutionType']?>',
                    subType: 0,
                    responsible: 0,
                    responsibleFullName: '',
                    dealStage: '<?=$arParams['dealStage']?>',
                },
                list: {
                    subType: [],
                },
                additionalData: {
                    isFirstShow: true,
                },
            }
        },
        methods: {
            getConstructSubTypes(constructId) {
                let root = this;
                let action = 'getConstructSubTypes';
                BX.ajax.runComponentAction(
                    this.component,
                    action,
                    {
                        mode: 'class',
                        data: {
                            constructId: constructId,
                        },
                    },
                ).then(function (response) {
                    if (response.status === 'success') {
                        root.list.subType = response.data;
                        root.solution.subType = root.list.subType[0].id;
                    } else {
                        console.error(root.error + action);
                    }
                });
            },
            getSolution() {
                let root = this;
                let action = 'getSolution';
                BX.ajax.runComponentAction(
                    this.component,
                    action,
                    {
                        mode: 'class',
                        data: {
                            construct: root.solution.construct,
                            type: root.solution.type,
                            id: root.solution.id,
                        },
                    },
                ).then(function (response) {
                    if (response.status === 'success') {
                        root.solution.id = response.data.id;
                        root.solution.responsible = response.data.responsible;
                        root.solution.responsibleFullName = response.data.responsibleFullName;
                        root.solution.subType = response.data.subType;
                    } else {
                        console.error(root.error + action);
                    }
                });
            },
            saveSolution() {
                if (!this.validateData()) {
                    return;
                }
                let root = this;
                let action = 'saveSolution';
                BX.ajax.runComponentAction(
                    this.component,
                    action,
                    {
                        mode: 'class',
                        data: {
                            id: root.solution.id,
                            construct: root.solution.construct,
                            type: root.solution.type,
                            subType: root.solution.subType,
                            responsible: root.solution.responsible,
                            dealStage: root.solution.dealStage,
                        },
                    },
                ).then(function (response) {
                    if (response.status === 'success') {
                        root.slider.data.data.status = 'save';
                        root.slider.data.data.constructType = '<?=$arParams['constructType']?>';
                        root.closeSlider();
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

                let status = true;

                // Проверяем данные перед сохранением
                if (this.solution.type !== 'ACTUAL_SALE') {
                    let user = document.querySelector('div.ui-tag-selector-outer-container');
                    if (this.solution.responsible === null) {
                        user.classList.add('ithive-prof-pclse-invalid');
                        status = false;
                    } else {
                        user.classList.remove('ithive-prof-pclse-invalid');
                    }
                }

                let inputBoxes = document.querySelectorAll('div.ithive-prof-pclse-input-field');
                if (this.solution.subType === 0) {
                    for (let i = 0; i < inputBoxes.length; i++) {
                        inputBoxes[i].classList.add('ithive-prof-pclse-invalid');
                    }
                    status = false;
                } else {
                    for (let i = 0; i < inputBoxes.length; i++) {
                        inputBoxes[i].classList.remove('ithive-prof-pclse-invalid');
                    }
                }

                return status;
            }
        },
        mounted() {
            this.getConstructSubTypes(this.solution.construct);
            this.getSlider();
            this.getSolution();
        },
        watch: {
            'solution.responsible': {
                handler: function (before, after) {
                    if (this.additionalData.isFirstShow === true)
                    {
                        this.$el.querySelectorAll('div.ui-tag-selector-tag-title')[0].innerText = this.solution.responsibleFullName;
                        this.additionalData.isFirstShow = false;
                    }
                },
            },
        }
    });

</script>
