<template>
    <table class="log-table">
        <tr>
            <td class="log-table-head">{{ localize.DATA }}</td>
            <td class="log-table-head">{{ localize.AUTHOR }}</td>
            <td class="log-table-head">{{ localize.TYPE }}</td>
            <td class="log-table-head">{{ localize.DESCRIPTION }}</td>
            <td class="log-table-head">{{ localize.COMMENT }}</td>
        </tr>
        <tr v-for="(log, index) in logs" :key="'log_' + index">
            <td class="log-table-column">{{ log.MODIFIED }}</td>
            <td class="log-table-column">{{ log.MODIFIED_BY }}</td>
            <td class="log-table-column">{{ log.ACTION_NAME }}</td>
            <td class="log-table-column">{{ log.ACTION_TITLE }}</td>
            <td class="log-table-column">{{ log.ACTION_NOTE }}</td>
        </tr>
    </table>
</template>

<script>
import {mapState} from "vuex";

export default {
    name: "Logs",
    computed: {
        ...mapState([
            'logs'
        ]),
        localize()
        {
            return Object.freeze({
                'DATA': BX.message("DATA"),
                'AUTHOR': BX.message("AUTHOR"),
                'TYPE': BX.message("TYPE"),
                'DESCRIPTION': BX.message("DESCRIPTION"),
                'COMMENT': BX.message("COMMENT"),
            })
        }
    }
}
</script>
