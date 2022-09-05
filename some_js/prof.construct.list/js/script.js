'use strict';

// ithive:prof.construct.list
const component = 'ithive:prof.construct.list';
const error = 'error has been occurred - ajax action: ';

/* region: Общие функции */

// Аккордеон конструктивов
function accordion(element) {
    let accordionCurrentStatus = element.dataset.status;
    let container;
    let selectorClass;
    if (element.dataset.type === 'dropdown-summary') {
        container = element.parentElement;
        selectorClass = 'ithive-prof-pcl-type-summary-content';
    }
    if (element.dataset.type === 'dropdown-construct') {
        container = element.parentElement.parentElement;
        selectorClass = 'ithive-prof-pcl-sub-content';
    }
    for (let i = 1; i < container.childNodes.length; i++) {
        if (container.childNodes[i].tagName === 'DIV') {
            if (container.childNodes[i].classList.contains(selectorClass)) {
                if (accordionCurrentStatus === 'open') {
                    container.childNodes[i].style.display = 'none';
                } else {
                    container.childNodes[i].style.display = 'block';
                }
            }
        }
    }
    if (accordionCurrentStatus === 'open') {
        element.dataset.status = 'closed';
        element.classList.remove('fa-angle-up');
        element.classList.add('fa-angle-down');
    } else {
        element.dataset.status = 'open';
        element.classList.remove('fa-angle-down');
        element.classList.add('fa-angle-up');
    }
}

/* endregion */

/* region: AJAX вызовы компонента */

// Редактирования комментария к конструктиву
function editConstructComment(element) {
    let action = 'editConstructComment';
    // Получение DOM-элементов
    let pencil = element;
    let comment = BX.findPreviousSibling(element);
    let check = BX.findPreviousSibling(comment);
    let input = BX.findPreviousSibling(check);
    // Изменение DOM-элементов
    pencil.style.display = 'none';
    comment.style.display = 'none';
    input.style.display = 'inline';
    check.style.display = 'inline';
    input.setAttribute('value', comment.innerText);
    // AJAX
    BX.bind(check, 'click', function () {
        BX.ajax.runComponentAction(
            component,
            action,
            {
                mode: 'class',
                data: {
                    constructId: element.dataset.constructId,
                    constructComment: input.value,
                },
            },
        ).then(function (response) {
            if (response.status === 'success') {
                // Изменение DOM-элементов
                pencil.style.display = 'inline-block';
                comment.style.display = 'inline';
                input.style.display = 'none';
                check.style.display = 'none';
                comment.innerText = input.value;
            } else {
                console.error(error + action);
            }
        });
    });
}

// Удаление конструктива и данных связанных с ним
function deleteConstruct(element) {
    let action = 'deleteConstruct';
    // Получение DOM-элементов
    let block = element.parentElement.parentElement;
    // Подготовка данных к отправке
    let data = {
        constructId: element.dataset.constructId,
    };
    // AJAX
    BX.ajax.runComponentAction(
        component,
        action,
        {
            mode: 'class',
            data: data,
        },
    ).then(function (response) {
        if (response.status === 'success') {
            // Изменение DOM-элементов
            block.remove();
            reloadConstructDealInfo();
            BX.onCustomEvent('IthiveProf::needRefreshSelector', [this]);
        } else {
            console.error(error + action);
        }
    });
}

// Удаление проектного решения и данных связанных с ним
function deleteProjectSolution(element) {
    let action = 'deleteProjectSolution';
    // Получение DOM-элементов
    let pencil = element;
    let cross = element.nextSibling;
    let table = element.parentElement.nextSibling;
    // Подготовка данных к отправке
    let data = {
        projectSolutionId: element.dataset.projectSolutionId,
    };
    // AJAX
    BX.ajax.runComponentAction(
        component,
        action,
        {
            mode: 'class',
            data: data,
        },
    ).then(function (response) {
        if (response.status === 'success') {
            // Изменение DOM-элементов
            pencil.remove();
            cross.remove();
            table.remove();
            reloadConstructDealInfo();
            BX.onCustomEvent('IthiveProf::needRefreshSelector', [this]);
        } else {
            console.error(error + action);
        }
    });
}

/* endregion */

/* region: Вызов компонентов в слайдере */

// Вызов prof.construct.list.solution.edit
function addSolution(element, additionalData) {
    // Подготовка данных к отправке
    let data = {};
    if (additionalData) {
        data = additionalData;
    } else {
        data = {
            constructId: element.dataset.constructId,
            solutionType: element.dataset.solutionType,
            dealStage: BX('deal-status').value,
            constructType: BX('ithive_prof_pcs_selector').getElementsByClassName('ithive-prof-pcs-menu-item')[0].dataset.constructType,
        };
    }

    if (element !== null && element !== undefined) {
        if (element.dataset.projectSolutionId) {
            data.projectSolutionId = element.dataset.projectSolutionId;
        }
    }

    // Вызов слайдера
    BX.SidePanel.Instance.open('ithive:prof.construct.list.solution.edit', {
        contentCallback: function (slider) {
            return new Promise(function (resolve, reject) {
                BX.ajax.runComponentAction(
                    'ithive:prof.construct.selector',
                    'getComponent',
                    {
                        mode: 'class',
                        data: {
                            componentName: 'ithive:prof.construct.list.solution.edit',
                            componentTemplate: '',
                            componentParams: data,
                        }
                    },
                ).then(function (response) {
                    let content = '';
                    let string = response.data.assets.string;
                    let css = response.data.assets.css;
                    let js = response.data.assets.js;
                    string.forEach(function (data) {
                        content = content + data
                    });
                    css.forEach(function (data) {
                        content = content + '<link href="' + data + '" type="text/css" rel="stylesheet">'
                    });
                    js.forEach(function (data) {
                        content = content + '<script type="text/javascript" src="' + data + '"></script>'
                    });
                    content = content + response.data.html;
                    resolve({
                        html: content,
                    });
                });
            });
        },
        events: {
            onClose: function (event) {
                //(слайдер закрыт с сохранением данных)
                if (event.slider.data.data.status === 'save') {
                    BX.onCustomEvent('IthiveProf::needRefreshSelector', [this]);
                }
                // event.slider.data.data.status === 'cancel' (слайдер закрыт без сохранения данных)
                if (event.slider.data.data.constructType) {
                    BX('ithive_prof_pcs_menu').querySelector('div[data-construct-type="' + event.slider.data.data.constructType + '"]').click();
                }
            }
        },
        cacheable: false,
        animationDuration: 100,
        width: 600,
        label: {
            bgColor: '#cd4c0b',
            opacity: 100
        },
    });
}

// Вызов prof.construct.list.material.edit
function editMaterial(element, additionalData) {
    // Подготовка данных к отправке
    let data = {};
    if (additionalData) {
        data = additionalData;
    } else {
        data = {
            subConstructId: element.dataset.subConstructId,
            solutionId: element.dataset.projectSolutionId,
            solutionType: element.dataset.solutionType,
            systemId: element.dataset.systemId,
            dealStage: BX('deal-status').value,
            constructType: BX('ithive_prof_pcs_selector').getElementsByClassName('ithive-prof-pcs-menu-item')[0].dataset.constructType,
            responsibleProject: element.dataset.responsibleProjectId ? element.dataset.responsibleProjectId : null,
        };
    }
    // Вызов слайдера
    BX.SidePanel.Instance.open('ithive:prof.construct.list.material.edit', {
        contentCallback: function (slider) {
            return new Promise(function (resolve, reject) {
                BX.ajax.runComponentAction(
                    'ithive:prof.construct.selector',
                    'getComponent',
                    {
                        mode: 'class',
                        data: {
                            componentName: 'ithive:prof.construct.list.material.edit',
                            componentTemplate: '',
                            componentParams: data,
                        }
                    },
                ).then(function (response) {
                    let content = '';
                    let string = response.data.assets.string;
                    let css = response.data.assets.css;
                    let js = response.data.assets.js;
                    string.forEach(function (data) {
                        content = content + data
                    });
                    css.forEach(function (data) {
                        content = content + '<link href="' + data + '" type="text/css" rel="stylesheet">'
                    });
                    js.forEach(function (data) {
                        content = content + '<script type="text/javascript" src="' + data + '"></script>'
                    });
                    content = content + response.data.html;
                    resolve({
                        html: content,
                    });
                });
            });
        },
        events: {
            onClose: function (event) {
                reloadConstructDealInfo();
                //(слайдер закрыт с сохранением данных)
                if (event.slider.data.data.status === 'save') {
                    BX.onCustomEvent('IthiveProf::needRefreshSelector', [this]);
                }
                // event.slider.data.data.status === 'cancel' (слайдер закрыт без сохранения данных)
                if (event.slider.data.data.constructType) {
                    BX('ithive_prof_pcs_menu').querySelector('div[data-construct-type="' + event.slider.data.data.constructType + '"]').click();
                }
            }
        },
        cacheable: false,
        animationDuration: 100,
        width: 600,
        label: {
            bgColor: '#cd4c0b',
            opacity: 100
        },
    });
}



// Вызов prof.construct.list.result.edit
function addResult(element) {
    // Подготовка данных к отправке
    let data = {
        dealId: BX('deal-id').value,
        materialId: element.dataset.materialId,
        solutionType: element.dataset.solutionType,
        canSave: element.dataset.canSave,
        constructType: BX('ithive_prof_pcs_selector').getElementsByClassName('ithive-prof-pcs-menu-item')[0].dataset.constructType,
    };
    // Вызов слайдера
    BX.SidePanel.Instance.open('ithive:prof.construct.list.result.edit', {
        contentCallback: function (slider) {
            return new Promise(function (resolve, reject) {
                BX.ajax.runComponentAction(
                    'ithive:prof.construct.selector',
                    'getComponent',
                    {
                        mode: 'class',
                        data: {
                            componentName: 'ithive:prof.construct.list.result.edit',
                            componentTemplate: '',
                            componentParams: data,
                        }
                    },
                ).then(function (response) {
                    let content = '';
                    let css = response.data.assets.css;
                    let js = response.data.assets.js;
                    css.forEach(function (data) {
                        content = content + '<link href="' + data + '" type="text/css" rel="stylesheet">'
                    });
                    js.forEach(function (data) {
                        content = content + '<script type="text/javascript" src="' + data + '"></script>'
                    });
                    content = content + response.data.html;
                    resolve({
                        html: content,
                    });
                });
            });
        },
        events: {
            onClose: function (event) {
                reloadConstructDealInfo();
                //(слайдер закрыт с сохранением данных)
                if (event.slider.data.data.status === 'save') {
                    BX.onCustomEvent('IthiveProf::needRefreshSelector', [this]);
                }
                // event.slider.data.data.status === 'cancel' (слайдер закрыт без сохранения данных)
                if (event.slider.data.data.constructType) {
                    BX('ithive_prof_pcs_menu').querySelector('div[data-construct-type="' + event.slider.data.data.constructType + '"]').click();
                }
            }
        },
        cacheable: false,
        animationDuration: 100,
        width: 600,
        label: {
            bgColor: '#cd4c0b',
            opacity: 100
        },
    });
}

// Вызов prof.construct.list.prematerial.edit
function preMaterialEdit(element) {
    // Подготовка данных к отправке
    let data = {
        subConstructId: element.dataset.subConstructId,
        constructId: element.dataset.constructId,
        dealStage: BX('deal-status').value,
        workTypeCode: element.dataset.workTypeCode,
        projectSolutionId: element.dataset.projectSolutionId, // ID "Проектное решение" PROFProjectSolution ХБ
        ourOfferId: element.dataset.ourOfferId, // ID "Наше предложение" PROFProjectSolution ХБ
        totalSolutionId: element.dataset.totalSolutionId, // ID "Итоговое решение" PROFProjectSolution ХБ
        solutionType: element.dataset.solutionType, // Тип проектного решения
        constructType: BX('ithive_prof_pcs_selector').getElementsByClassName('ithive-prof-pcs-menu-item')[0].dataset.constructType,
    };
    // Вызов слайдера
    BX.SidePanel.Instance.open('ithive:prof.construct.list.prematerial.edit', {
        contentCallback: function (slider) {
            return new Promise(function (resolve, reject) {
                BX.ajax.runComponentAction(
                    'ithive:prof.construct.selector',
                    'getComponent',
                    {
                        mode: 'class',
                        data: {
                            componentName: 'ithive:prof.construct.list.prematerial.edit',
                            componentTemplate: '',
                            componentParams: data,
                        }
                    },
                ).then(function (response) {
                    let content = '';
                    let css = response.data.assets.css;
                    let js = response.data.assets.js;
                    css.forEach(function (data) {
                        content = content + '<link href="' + data + '" type="text/css" rel="stylesheet">'
                    });
                    content = content + '<link href="/bitrix/css/main/font-awesome.min.css" type="text/css" rel="stylesheet">';
                    js.forEach(function (data) {
                        content = content + '<script type="text/javascript" src="' + data + '"></script>'
                    });
                    content = content + response.data.html;
                    resolve({
                        html: content,
                    });
                });
            });
        },
        events: {
            onClose: function (event) {
                reloadConstructDealInfo();
                //(слайдер закрыт с сохранением данных)
                if (event.slider.data.data.status === 'save') {
                    BX.onCustomEvent('IthiveProf::needRefreshSelector', [this]);
                }
                // event.slider.data.data.status === 'cancel' (слайдер закрыт без сохранения данных)
                if (event.slider.data.data.status === 'save') {
                    if (event.slider.data.data.selectedMode === 'SELF') {
                        // По какой-то причине Bitrix не успевает обработать VUE - поэтому используется setTimeout
                        setTimeout(function(){
                            addSolution(null, {
                                constructId: event.slider.data.data.constructId,
                                solutionType: event.slider.data.data.solutionType,
                                dealStage: BX('deal-status').value,
                                constructType: event.slider.data.data.constructType,
                            });
                        },500);
                    } else {
                        editMaterial(null, {
                            subConstructId: event.slider.data.data.subConstructId,
                            solutionId: event.slider.data.data.solutionId,
                            solutionType: event.slider.data.data.solutionType,
                            dealStage: BX('deal-status').value,
                            constructType: event.slider.data.data.constructType,
                            responsibleProject: event.slider.data.data.responsibleProject,
                            systemId: event.slider.data.data.systemId,
                        });
                    }
                }
            }
        },
        cacheable: false,
        animationDuration: 100,
        width: 600,
        label: {
            bgColor: '#cd4c0b',
            opacity: 100
        },
    });
}

// Вызов prof.construct.deal.info - перерендеринг блоков сводной информации о конструктивах
function reloadConstructDealInfo(dealId) {
    // Подготовка данных к отправке
    let data = {
        DEAL_ID: dealId ? dealId : BX('deal-id').value,
    };

    // Перерендеринг компонента
    BX.ajax.runComponentAction(
        component,
        'getComponent',
        {
            mode: 'class',
            data: {
                componentName: 'ithive:prof.construct.deal.info',
                componentTemplate: '',
                componentParams: data,
            }
        },
    ).then(function (response) {
        if (response.status === 'success') {
            let content = response.data.html;
            if (content) {
                let old = BX('ithive_prof_pcdi');
                if (old) {
                    old.insertAdjacentHTML('beforebegin', content);
                    old.remove();
                }
            }
        } else {
            console.error(error + action);
        }
    });
}

// Открытие спецификации
function openSpecification(dealId, specId) {
    BX.SidePanel.Instance.open("/local/components/ithive/specification.edit/specification_tab.php?id=" + specId + "&deal=" + dealId, {
        allowChangeHistory: false,
        cacheable: false,
        label: {
            text: BX.message('SLIDER_AGREEMENT_TITLE')
        }
    });
}

/* endregion */