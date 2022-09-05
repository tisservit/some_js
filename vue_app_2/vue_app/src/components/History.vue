<template>
    <div class="block-process" style="margin-bottom: 80px;">
        <template v-if="prevRating.length > 0">
            <div class="title-block-process">{{ localize.INFO_RATING }}</div>
            <table class="history-table">
                <tr v-for="(rating, index) in prevRating" :key="'prev_rating_' + index">
                    <td class="history-column">{{ localize.REPORT_RATING }} {{ index + 1 }}</td>
                    <td class="history-column">{{ rating }}</td>
                </tr>
            </table>
        </template>
    </div>
</template>

<script>
    import {mapState} from "vuex";

    export default {
        name: "History",
        computed: {
            ...mapState([
                'prevRating'
            ]),
            localize()
            {
                return Object.freeze({
                    'INFO_RATING': BX.message("INFO_RATING"),
                    'REPORT_RATING': BX.message("REPORT_RATING")
                })
            }
        }
    }
</script>
