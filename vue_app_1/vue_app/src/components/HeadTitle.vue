<template>
    <div class="form-title">
        <span class="form-title-label">{{ localize.GOALS_EMPLOYEE }} {{ userInfo.NAME }}</span>
    </div>
</template>

<script>
    import {mapState} from "vuex";

    export default {
        name: "HeadTitle",
        props: {
            title: String
        },
        computed: {
            ...mapState([
                'userInfo'
            ]),
            localize()
            {
                return Object.freeze({
                    'GOALS_EMPLOYEE': BX.message("GOALS_EMPLOYEE")
                })
            }
        }
    }
</script>