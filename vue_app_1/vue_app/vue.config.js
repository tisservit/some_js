module.exports = {
    publicPath          : '/bitrix/components/ithive.goalsbazhen/edit.card.goals/templates/.default/vue_app/dist/',
    filenameHashing     : false,
    productionSourceMap : false,
    chainWebpack        : config => {
        config.plugins.delete('html')
        config.plugins.delete('preload')
        config.plugins.delete('prefetch')
    }
};