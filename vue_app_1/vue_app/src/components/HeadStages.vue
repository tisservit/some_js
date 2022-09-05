<template>
    <div class="crm-entity-section-status-container-flex">
        <div class="crm-entity-section-status-step" v-for="(status, index) in statusGoals" :key="'status_goal_' + index">
            <div class="crm-entity-section-status-step-item">
                <div data-base-color="#00C4FB" class="crm-entity-section-status-step-item-text" v-bind:style="{ borderImage: status.background, color: status.color}">
                    {{ status.value }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {mapState} from "vuex";

    export default {
        name: "HeadStages",
        computed: {
            ...mapState([
                'statusGoals'
            ])
        }
    }
</script>

