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

<div id="prof_construct_list_material_edit_vue">

    <div class="ithive-prof-pclme">

        <h6 class="ithive-prof-pclme-main-title"><?= GetMessage('TITLE') ?></h6>

        <input-select
                label="<?= GetMessage('SYSTEM') ?>"
                :options="list.system"
                logic="SYSTEM"
                v-model="solution.system.id"
        ></input-select>

        <div class="ithive-prof-pclme-main-content">

            <input-user
                    v-if="solution.type === 'TOTAL_SOLUTION'"
                    label="<?= GetMessage('PROJECT_RESPONSIBLE') ?>"
                    logic="common"
                    v-model="solution.responsible"
            ></input-user>

            <div class="ithive-prof-pclme-sub-content">


                <form-material
                        v-for="material in solution.material"
                        type="<?= $arParams['solutionType'] ?>"
                        logic="FORM"
                >
                </form-material>

                <button-add label="Добавить"></button-add>

            </div>

        </div>

    </div>

    <!-- SUB MENU -->
    <sub-menu></sub-menu>

</div>

<script>

    BX.Vue.component('input-number', {
        template: '' +
            '<div class="ithive-prof-pclme-input-box">' +
            '<label class="ithive-prof-pclme-input-label">{{label}}</label>' +
            '<input type="number" min="0" class="ui-ctl-element ithive-prof-pclme-input-field" v-model="getValue">' +
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
                    // let inputValue = this.$el.querySelector('input').value;
                    // switch (this.logic)
                    // {
                    //     case 'thickness':
                    //         /**/
                    //         break;
                    //     case 'volume':
                    //         /**/
                    //         break;
                    // }
                    if (value == 'null') {
                        value = null;
                    }
                    this.$emit('input', value);
                }
            }
        },
        methods: {
            // normalizeNumbers() {
            //     if (this.logic === 'thickness') {
            //         if (this.getValue % 10 !== 0) {
            //             let sv = this.getValue + '';
            //             let lsv = sv.slice(-1);
            //             if (sv.length > 1) {
            //                 if (0 < lsv && lsv < 5) {
            //                     this.getValue = this.getValue - lsv;
            //                 }
            //                 if (4 < lsv && lsv <= 9) {
            //                     this.getValue = (this.getValue + 10) - lsv;
            //                 }
            //                 if (this.getValue < 0) {
            //                     this.getValue = 0;
            //                 }
            //             }
            //             if (sv.length === 1) {
            //                 this.getValue = (this.getValue * 10);
            //             }
            //         }
            //         if (this.getValue === '0' || this.getValue < 0) {
            //             this.getValue = 10;
            //         }
            //     }
            // }
        },
    });

    BX.Vue.component('input-select', {
        template: '' +
            '<div class="ithive-prof-pclme-input-box">' +
            '<label class="ithive-prof-pclme-input-label">{{label}}</label>' +
            '<div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown ithive-prof-pclme-input-field">' +
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
            'logic'
        ],
        computed: {
            getValue: {
                get() {
                    return this.value;
                },
                set(value) {
                    let root = this.$root;
                    this.$emit('input', value);
                    if (this.logic === 'SYSTEM') {
                        root.changeSystem(value);
                    }
                }
            }
        },
    });

    BX.Vue.component('input-user', {
        template: '' +
            '<div class="ithive-prof-pclme-input-box">' +
            '<label class="ithive-prof-pclme-input-label">{{label}}</label>' +
            '<div style="margin-top: 5px">' +
            '</div>' +
            '</div>' +
            '',
        props: [
            'label',
            'value',
            'logic'
        ],
        mounted() {
            let app = this;
            this.$el.children[1].setAttribute('id', 'input-user-' + this._uid);
            let value = this.value;
            let tagSelector = new BX.UI.EntitySelector.TagSelector({
                multiple: false,
                items: [
                    {
                        id: (app.logic === 'common') ? app.$root.solution.projectResponsible : app.$parent.material.responsible,
                        entityId: (app.logic === 'common') ? 'U' + app.$root.solution.projectResponsible : 'U' + app.$parent.material.responsible,
                        title: (app.logic === 'common') ? '<?=$arResult['RESPONSIBLE_PROJECT_FULL_NAME']?>' : '',
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
                        value = event.data.tag.id;
                        switch (app.logic) {
                            case 'common':
                                app.$root.solution.projectResponsible = value;
                                break;
                            case 'material':
                                app.$parent.material.responsible = value;
                                break;
                        }
                    },
                    onTagRemove: (event) => {
                        switch (app.logic) {
                            case 'common':
                                app.$root.solution.projectResponsible = null;
                                break;
                            case 'material':
                                app.$parent.material.responsible = null;
                                break;
                        }
                        value = null;
                    }
                }
            });
            tagSelector.renderTo(document.getElementById('input-user-' + this._uid));
        },
    });

    BX.Vue.component('form-material', {
        template: '' +
            '<div>' +
            '<input-user label="<?=GetMessage('MATERIAL_SALE_RESPONSIBLE')?>" v-if="this.type === \'ACTUAL_SALE\'" v-model="material.responsible" logic="material"></input-user>' +
            '<input-select label="<?=GetMessage('MATERIAL_TYPE')?>" :options="list.direction" v-model="material.direction" :disabled="list.direction.length === 0"></input-select>' +
            '<input-select label="<?=GetMessage('MATERIAL_CONSTRUCT')?>" :options="list.material" v-model="material.material" :disabled="list.material.length === 0"></input-select>' +
            '<input-select label="<?=GetMessage('MATERIAL_MANUFACTURER')?>" :options="list.manufacturer" v-model="material.manufacturer" :disabled="list.manufacturer.length === 0"></input-select>' +
            '<input-select label="<?=GetMessage('MATERIAL_BRAND_TITLE')?>" :options="list.brandTitle" v-model="material.brandTitle" :disabled="list.brandTitle.length === 0"></input-select>' +
            '<input-number label="<?=GetMessage('MATERIAL_THICKNESS')?>" logic="thickness" v-if="this.type !== \'ACTUAL_SALE\'" v-model="material.thickness"></input-number>' +
            '<input-number label="#TITLE#" logic="volume" v-if="this.type !== \'PROJECT_SOLUTION\'" v-model="material.volume"></input-number>' +
            '<div class="ithive-prof-pclme-row ithive-prof-pclme-row-end">' +
            '<button class="ui-btn ui-btn-light-border ithive-prof-pclme-standard-button" @click="removeForm"><?=GetMessage('DELETE')?></button>' +
            '</div>' +
            '<hr class="ithive-prof-pclme-hr">' +
            '</div>' +
            '',
        props: [
            'type',
            'logic'
        ],
        methods: {
            setTitleVolumeInput() {
                let labelList = BX.findChildren(
                    this.$el,
                    {
                        tagName: 'label',
                    },
                    true,
                );
                for (let i = 0; i < labelList.length; i++) {
                    if (labelList[i].textContent === '#TITLE#') {
                        switch (this.$root.solution.type) {
                            case 'OUR_OFFER':
                                labelList[i].textContent = '<?=GetMessage('MATERIAL_POTENTIAL_SALE')?>';
                                break;
                            case 'TOTAL_SOLUTION':
                                labelList[i].textContent = '<?=GetMessage('MATERIAL_PLANE_SALE')?>';
                                break;
                            case 'ACTUAL_SALE':
                                labelList[i].textContent = '<?=GetMessage('MATERIAL_ACTUAL_SALE')?>';
                                break;
                        }
                    }
                }
            },
            cleanSelected(watch) {
                switch (watch) {
                    case 'material.direction':
                        this.material.material = null;
                        this.material.manufacturer = null;
                        this.material.brandTitle = null;
                        break;
                    case 'material.material':
                        this.material.manufacturer = null;
                        this.material.brandTitle = null;
                        break;
                    case 'material.manufacturer':
                        this.material.brandTitle = null;
                        break;
                }
            },
            removeForm() {
                let form = this;
                form.$el.remove();
                form.$destroy();
                let materialStorage = this.$root.solution.material;
                this.$root.throwSystem();
                materialStorage.forEach(function (material, index) {
                    if (material.id === form.material.id || material.id === form._uid + '_runtime') {
                        delete materialStorage[index];
                    }
                });
            },
        },
        data() {
            return {
                list: {
                    direction: [],
                    material: [],
                    manufacturer: [],
                    brandTitle: [],
                },
                material: {
                    id: this._uid + '_runtime',
                    direction: null,
                    material: null,
                    manufacturer: null,
                    brandTitle: null,
                    thickness: null,
                    volume: null,
                    responsible: <?=$USER->GetID()?>,
                    responsibleFullName: '<?=$arResult['RESPONSIBLE_MATERIAL_FULL_NAME']?>',
                },
                additionalData: {
                    isFirstShow: true,
                }
            };
        },
        mounted() {
            let form = this;
            let root = form.$root;
            this.setTitleVolumeInput();
            root.getDirections(this);
            root.addEntityToMaterialStorage(this);
            if (root.solution.material.length > 0) {
                form.material = root.solution.material[root.loadedDataIndex];
                root.loadedDataIndex++;
            }
            if (form.additionalData.isFirstShow === true) {
                if ('<?=$arParams['solutionType']?>' === 'ACTUAL_SALE') {
                    form.$el.querySelectorAll('div.ui-tag-selector-tag-title')[0].innerText = form.material.responsibleFullName;
                }
                this.additionalData.isFirstShow = false;
            }
            if ('<?=$arParams['solutionType']?>' === 'ACTUAL_SALE') {
                if (form.$el.querySelector('div.ui-tag-selector-tag-title').innerText === '') {
                    form.$el.querySelector('div.ui-tag-selector-tag-title').innerText = '<?=$arResult['RESPONSIBLE_PROJECT_FULL_NAME']?>';
                }
            }
        },
        watch: {
            'material.direction': {
                handler: function (before, after) {
                    // alert('before >>> '+before+' after >>> '+after);
                    this.$root.getMaterials(this);
                    if (before && after) {
                        this.cleanSelected('material.direction');
                        this.$root.throwSystem();
                    }
                },
            },
            'material.material': {
                handler: function (before, after) {
                    // alert('before >>> '+before+' after >>> '+after);
                    this.$root.getManufactures(this);
                    if (before && after) {
                        this.cleanSelected('material.material');
                        this.$root.throwSystem();
                    }
                },
            },
            'material.manufacturer': {
                handler: function (before, after) {
                    // alert('before >>> '+before+' after >>> '+after);
                    this.$root.getBrandTitles(this);
                    if (before && after) {
                        this.cleanSelected('material.manufacturer');
                        this.$root.throwSystem();
                    }
                },
            },
        }
    });

    BX.Vue.component('button-add', {
        template: '' +
            '<div>' +
            '<button style="margin-left: 0" class="ui-btn ui-btn-primary ithive-prof-pcl-standard-button" @click="addMaterial">' +
            '<i class="fa fa-plus" style="margin-right: 10px; color: #ffffff; cursor: pointer;"></i>' +
            '{{label}}</button>' +
            '</div>' +
            '',
        props: [
            'label',
        ],
        methods: {
            addMaterial() {
                this.$root.solution.material.push('new');
                this.$root.throwSystem();
            },
        },
    });

    BX.Vue.component('sub-menu', {
        template: '' +
            '<div class="ithive-prof-pclme-sub-menu">' +
            '<div class="ithive-prof-pclme-row">' +
            '<button class="ui-btn ui-btn-primary ithive-prof-pcl-standard-button" @click="save"><?= GetMessage('SAVE') ?></button>' +
            '<a href="#" class="ui-btn ui-btn-link" @click="close"><?= GetMessage('CANCEL') ?></a>' +
            <?php global $USER; ?>
            <?php if($USER->IsAdmin()):?>
            '' +
            '<button class="ui-btn ui-btn-secondary ithive-prof-pcl-standard-button ithive-prof-pclme-sub-menu-item-end" @click="systemSave"><?= GetMessage('SYSTEM_SAVE') ?></button>' +
            '' +
            <?php endif;?>
            '</div>' +
            '</div>' +
            '',
        methods: {
            close() {
                this.$root.closeSlider();
            },
            save() {
                this.$root.saveMaterials();
            },
            systemSave() {
                let root = this.$root;
                let popupId = 'ithive_prof_pclme_save_system_popup';
                let popupEntity = BX.PopupWindowManager.create(
                    popupId,
                    null,
                    {
                        titleBar: '<?=GetMessage("SET_SYSTEM_TITLE")?>',
                        content: `
                        <input type="text" class="ui-ctl-element ithive-prof-pclme-input-field" id="ithive_prof_pclme_system_title">
                        `,
                        buttons: [
                            new BX.PopupWindowButton({
                                text: '<?=GetMessage("SAVE")?>', // текст кнопки
                                id: 'ithive_prof_pclme_save_system_popup_save', // идентификатор кнопки
                                className: 'ui-btn ui-btn-success', // доп. классы
                                events: {
                                    click: function () {
                                        // Событие при клике на кнопку
                                        let systemTitle = BX('ithive_prof_pclme_system_title').value;
                                        if (systemTitle.length === 0) {
                                            alert('<?=GetMessage("SET_SYSTEM_TITLE")?>');
                                        } else {
                                            // Процесс сохранения системы
                                            root.saveSystem(systemTitle);
                                            popupEntity.close();
                                        }
                                    }
                                }
                            }),

                            new BX.PopupWindowButton({
                                text: '<?=GetMessage("CANCEL")?>', // текст кнопки
                                id: 'ithive_prof_pclme_save_system_popup_cancel', // идентификатор кнопки
                                className: 'ui-btn ui-btn-primary', // доп. классы
                                events: {
                                    click: function () {
                                        // Событие при клике на кнопку
                                        popupEntity.close();
                                    }
                                }
                            })
                        ],
                        events: {
                            onPopupShow: function () {
                                // Событие при показе окна
                                BX('ithive_prof_pclme_system_title').value = '';
                            },
                            onPopupClose: function () {
                                // Событие при закрытии окна
                            }
                        }
                    }
                );
                popupEntity.show();
            },
        },
    });

    var application = BX.Vue.create({
        el: '#prof_construct_list_material_edit_vue',
        data() {
            return {
                component: '<?=$this->getComponent()->getName()?>',
                error: 'error has been occurred - ajax action: ',
                slider: null,
                construct: {
                    subType: <?=$arParams['subConstructId']?>
                },
                solution: {
                    id: <?=$arParams['solutionId']?>,
                    type: '<?=$arParams['solutionType']?>',
                    projectResponsible: <?=$USER->GetID()?>,
                    system: {
                        id: <?=$arParams['systemId']?:0?>,
                        title: 0,
                    },
                    material: [],
                },
                list: {
                    system: []
                },
                loadedDataIndex: 0,
            }
        },
        mounted() {
            this.getSystems();
            this.getSlider();
            this.getSolutionMaterials();
        },
        methods: {
            getDirections(form) {
                let root = this;
                let action = 'getDirections';
                BX.ajax.runComponentAction(
                    this.component,
                    action,
                    {
                        mode: 'class',
                        data: {},
                    },
                ).then(function (response) {
                    if (response.status === 'success') {
                        form.list.direction = response.data;
                    } else {
                        console.error(root.error + action);
                    }
                });
            },
            getMaterials(form) {
                let root = this;
                let action = 'getMaterials';
                BX.ajax.runComponentAction(
                    this.component,
                    action,
                    {
                        mode: 'class',
                        data: {
                            direction: form.material.direction,
                        },
                    },
                ).then(function (response) {
                    if (response.status === 'success') {
                        form.list.material = response.data;
                    } else {
                        console.error(root.error + action);
                    }
                });
            },
            getManufactures(form) {
                let root = this;
                let action = 'getManufactures';
                BX.ajax.runComponentAction(
                    this.component,
                    action,
                    {
                        mode: 'class',
                        data: {
                            material: form.material.material,
                        },
                    },
                ).then(function (response) {
                    if (response.status === 'success') {
                        form.list.manufacturer = response.data;
                    } else {
                        console.error(root.error + action);
                    }
                });
            },
            getBrandTitles(form) {
                let root = this;
                let action = 'getBrandTitles';
                BX.ajax.runComponentAction(
                    this.component,
                    action,
                    {
                        mode: 'class',
                        data: {
                            direction: form.material.direction,
                            material: form.material.material,
                            manufacturer: form.material.manufacturer,
                        },
                    },
                ).then(function (response) {
                    if (response.status === 'success') {
                        form.list.brandTitle = response.data;
                    } else {
                        console.error(root.error + action);
                    }
                });
            },
            addEntityToMaterialStorage(form) {
                for (let i = 0; i < this.solution.material.length; i++) {
                    if (this.solution.material[i] === 'new') {
                        this.solution.material[i] = form.material;
                    }
                }
            },
            getSolutionMaterials() {
                let root = this;
                let action = 'getSolutionMaterials';

                BX.ajax.runComponentAction(
                    this.component,
                    action,
                    {
                        mode: 'class',
                        data: {
                            solutionId: root.solution.id,
                            solutionType: root.solution.type,
                        },
                    },
                ).then(function (response) {
                    if (response.status === 'success') {
                        response.data.forEach(function (data) {
                            root.solution.material.push(data);
                        });
                    } else {
                        console.error(root.error + action);
                    }
                });
            },
            saveMaterials() {

                if (!this.validateData()) {
                    return;
                }

                let root = this;
                let action = 'saveMaterials';

                let materialsData = [];
                root.solution.material.forEach(function (form) {
                    materialsData.push({
                        id: form.id ? form.id : null,
                        direction: form.direction ? form.direction : null,
                        material: form.material ? form.material : null,
                        manufacturer: form.manufacturer ? form.manufacturer : null,
                        brandTitle: form.brandTitle ? form.brandTitle : null,
                        thickness: form.thickness ? form.thickness : null,
                        volume: form.volume ? form.volume : null,
                        responsible: form.responsible ? form.responsible : null,
                    });
                });

                BX.ajax.runComponentAction(
                    this.component,
                    action,
                    {
                        mode: 'class',
                        data: {
                            systemId: root.solution.system.id,
                            solutionId: root.solution.id,
                            solutionType: root.solution.type,
                            subConstruct: <?=intval($arParams['subConstructId'])?>,
                            projectResponsible: root.solution.projectResponsible,
                            materials: materialsData,
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
            getSystems() {
                let root = this;
                let action = 'getSystems';
                BX.ajax.runComponentAction(
                    this.component,
                    action,
                    {
                        mode: 'class',
                        data: {
                            subType: root.construct.subType,
                        },
                    },
                ).then(function (response) {
                    if (response.status === 'success') {
                        root.list.system = response.data;
                    } else {
                        console.error(root.error + action);
                    }
                });
            },
            saveSystem(systemName) {
                let root = this;
                let action = 'saveSystem';

                BX.ajax.runComponentAction(
                    this.component,
                    action,
                    {
                        mode: 'class',
                        data: {
                            subType: root.construct.subType,
                            systemName: systemName,
                            materials: root.solution.material,
                        },
                    },
                ).then(function (response) {
                    if (response.status === 'success') {
                        root.getSystems();
                        root.solution.system.id = response.data;
                        root.changeSystem(root.solution.system.id);
                    } else {
                        console.error(root.error + action);
                    }
                });
            },
            changeSystem(systemId) {
                let root = this;
                let action = 'changeSystem';
                BX.ajax.runComponentAction(
                    this.component,
                    action,
                    {
                        mode: 'class',
                        data: {
                            systemId: systemId,
                        },
                    },
                ).then(function (response) {
                        if (response.status === 'success') {

                            // Добавляем материалы из новой системы
                            let newSystemId = 0;
                            response.data.forEach(function (data) {
                                if (newSystemId === 0) {
                                    newSystemId = data.system;
                                }
                                root.solution.material.push(data);
                            });

                            // Удаляем материалы из старой системы
                            for (let i = 0; i < root.$children.length; i++) {
                                if (root.$children[i].logic === 'FORM') {
                                    // if (root.$children[i].material.system !== newSystemId) {
                                        root.$children[i].$el.remove();
                                    // }
                                }
                            }
                            for (let i = 0; i < root.solution.material.length; i++) {
                                if (root.solution.material[i].system !== newSystemId) {
                                    root.solution.material[i] = [];
                                }
                            }

                        } else {
                            console.error(root.error + action);
                        }
                    }
                )
                ;
            },
            throwSystem() {
                if (this.solution.system.id > 0) {
                    this.solution.system.id = 0;
                }
            },
            validateData() {

                let valid = true;

                let rootNode = BX('prof_construct_list_material_edit_vue');
                let allSelectors = rootNode.querySelectorAll('select.ui-ctl-element');
                let allNumbers = rootNode.querySelectorAll('input.ui-ctl-element');
                let allUsers = rootNode.querySelectorAll('div.ui-tag-selector-outer-container');

                if (allSelectors) {
                    allSelectors.forEach(function (data, index) {
                        if (data.value.length === 0 && !data.hasAttribute('disabled')) {
                            if (!data.classList.contains('ithive-prof-pclme-invalid')) {
                                data.classList.add('ithive-prof-pclme-invalid');
                            }
                            valid = false;
                        } else {
                            if (data.classList.contains('ithive-prof-pclme-invalid')) {
                                data.classList.remove('ithive-prof-pclme-invalid');
                            }
                        }
                    });
                }

                if (allNumbers) {
                    allNumbers.forEach(function (data, index) {
                        if (
                            (data.value === '' || data.value < 0 || data.value === '0')
                            && !data.hasAttribute('disabled')
                            && data.previousSibling.innerText !== '<?=GetMessage('MATERIAL_ACTUAL_SALE')?>'
                            && data.previousSibling.innerText !== '<?=GetMessage('MATERIAL_THICKNESS')?>'
                        ) {
                            if (!data.classList.contains('ithive-prof-pclme-invalid')) {
                                data.classList.add('ithive-prof-pclme-invalid');
                            }
                            valid = false;
                        } else {
                            if (data.classList.contains('ithive-prof-pclme-invalid')) {
                                data.classList.remove('ithive-prof-pclme-invalid');
                            }
                        }
                    });
                }

                if (allUsers) {
                    allUsers.forEach(function (data, index) {
                        let userSelected = data.querySelector('div.ui-tag-selector-tag-title');
                        if (userSelected === null || userSelected === undefined) {
                            if (!data.classList.contains('ithive-prof-pclme-invalid')) {
                                data.classList.add('ithive-prof-pclme-invalid');
                            }
                            valid = false;
                        } else {
                            if (data.classList.contains('ithive-prof-pclme-invalid')) {
                                data.classList.remove('ithive-prof-pclme-invalid');
                            }
                        }
                    });
                }

                if (valid === false) {
                    alert('<?=GetMessage('NOT_ALL_FIELDS_FILLED')?>');
                }

                return valid;
            },
            getSlider() {
                let root = this;
                BX.SidePanel.Instance.openSliders.forEach(function (data, index) {
                    if (data.url === root.component) {
                        root.slider = BX.SidePanel.Instance.openSliders[index];
                    }
                });
            },
            closeSlider() {
                if (this.slider.data.data.status !== 'save') {
                    this.slider.data.data.status = 'cancel'
                }
                this.slider.close();
            },
        },
    });

</script>
