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

Extension::load(['ui.forms', 'ui.buttons.icons', 'ui.vue', 'sidepanel']);

?>

<div id="prof_construct_list_construct_vue">

    <div class="ithive-prof-pclce">

        <h6 class="ithive-prof-pclce-main-title"><?= GetMessage('TITLE') ?></h6>

        <div class="ithive-prof-pclce-main-content">

            <input-select
                    label="<?= GetMessage('CONSTRUCT_TYPE') ?>"
                    v-model="construct.type"
                    :options="list.constructType"
            ></input-select>

<!--            <input-select-->
<!--                    label="--><?php //= GetMessage('CONSTRUCT_SUB_TYPE') ?><!--"-->
<!--                    v-model="construct.subType"-->
<!--                    :options="list.constructSubType"-->
<!--            ></input-select>-->

            <input-text
                    label="<?= GetMessage('CONSTRUCT_COMMENT') ?>"
                    v-model="construct.comment"
            ></input-text>

            <input-select
                    label="<?= GetMessage('CONSTRUCT_WORK_TYPE') ?>"
                    :options="list.workType"
                    v-model="construct.workType"
            ></input-select>

        </div>

    </div>

    <!-- SUB MENU -->
    <sub-menu></sub-menu>

</div>

<script>

    BX.Vue.component('input-select', {
        template: '' +
            '<div class="ithive-prof-pclce-input-box">' +
            '<label class="ithive-prof-pclce-input-label">{{label}}</label>' +
            '<div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown ithive-prof-pclce-input-field">' +
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

    BX.Vue.component('input-text', {
        template: '' +
            '<div class="ithive-prof-pclce-input-box">' +
            '<label class="ithive-prof-pclce-input-label">{{label}}</label>' +
            '<input type="text" class="ui-ctl-element ithive-prof-pclce-input-field" v-model="getValue">' +
            '</div>' +
            '',
        props: [
            'label',
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
        }
    });

    BX.Vue.component('sub-menu', {
        template: '' +
            '<div class="ithive-prof-pclce-sub-menu">' +
            '<div class="ithive-prof-pclce-row">' +
            '<button class="ui-btn ui-btn-primary ithive-prof-pcl-standard-button" @click="save"><?= GetMessage('SAVE') ?></button>' +
            '<a href="#" class="ui-btn ui-btn-link" @click="close"><?= GetMessage('CANCEL') ?></a>' +
            '</div>' +
            '</div>' +
            '',
        methods: {
            close()
            {
                this.$root.closeSlider();
            },
            save()
            {
                this.$root.saveConstruct();
            },
        }
    });

    var application = BX.Vue.create({
        el: '#prof_construct_list_construct_vue',
        data() {
            return {
                component: '<?=$this->getComponent()->getName()?>',
                error: 'error has been occurred - ajax action: ',
                slider: null,
                list: {
                    constructType: [],
                    // constructSubType: [],
                    workType: [],
                },
                construct: {
                    type: <?=$arResult['CONSTRUCT_TYPE']?>,
                    //subType: <?php //=$arResult['CONSTRUCT_SUB_TYPE']?>//,
                    comment: '',
                    workType: <?=$arResult['CONSTRUCT_WORK_TYPE']?>,
                }
            }
        },
        mounted() {
            this.getSlider();
            this.getConstructTypes();
            // this.getConstructSubTypes(this.construct.type);
            this.getConstructWorkTypes();
        },
        methods: {
            getConstructTypes() {
                let root = this;
                let action = 'getConstructTypes';
                BX.ajax.runComponentAction(
                    this.component,
                    action,
                    {
                        mode: 'class',
                        data: {},
                    },
                ).then(function (response) {
                    if (response.status === 'success') {
                        root.list.constructType = response.data;
                    } else {
                        console.error(root.error + action);
                    }
                });
            },
            // getConstructSubTypes(constructType) {
            //     let root = this;
            //     let action = 'getConstructSubTypes';
            //     BX.ajax.runComponentAction(
            //         this.component,
            //         action,
            //         {
            //             mode: 'class',
            //             data: {
            //                 constructType: constructType,
            //             },
            //         },
            //     ).then(function (response) {
            //         if (response.status === 'success') {
            //             root.list.constructSubType = response.data;
            //             root.construct.subType = root.list.constructSubType[0].id;
            //         } else {
            //             console.error(root.error + action);
            //         }
            //     });
            // },
            getConstructWorkTypes() {
                let root = this;
                let action = 'getConstructWorkTypes';
                BX.ajax.runComponentAction(
                    this.component,
                    action,
                    {
                        mode: 'class',
                        data: {},
                    },
                ).then(function (response) {
                    if (response.status === 'success') {
                        root.list.workType = response.data;
                    } else {
                        console.error(root.error + action);
                    }
                });
            },
            saveConstruct() {
                let root = this;
                let action = 'saveConstruct';
                BX.ajax.runComponentAction(
                    this.component,
                    action,
                    {
                        mode: 'class',
                        data: {
                            dealId: <?=intval($arParams['dealId'])?>,
                            type: root.construct.type,
                            // subType: root.construct.subType,
                            comment: root.construct.comment,
                            workType: root.construct.workType,
                        },
                    },
                ).then(function (response) {
                    if (response.status === 'success') {
                        root.slider.data.data.status = 'save';
                        root.slider.data.data.constructType = response.data;
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
        },
        // watch: {
        //     'construct.type': {
        //         handler: function (after, before) {
        //             this.getConstructSubTypes(after);
        //         },
        //         deep: true
        //     }
        // },

    })
</script>
