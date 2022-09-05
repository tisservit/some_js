<template>
    <div class="block-process">
        <div class="title-block-process">{{ localize.ABOUT_PROCESS }}</div>
        <div class="block-process-info">
            <div class="sub-block-info-user">
                <span class="sub-block-info-title">{{ localize.ASSESSED }}</span>
                <div class="sub-block-info-employee">
                    <div class="sub-block-info-inline" v-if="userInfo.ICON">
                        <img class="sub-block-info-icon" :src="userInfo.ICON">
                    </div>
                    <div class="sub-block-info-inline">
                        <span class="sub-block-info-name">
                            <a :href="'/company/personal/user/' + userInfo.ID + '/'">{{ userInfo.NAME }}</a></span>
                        <span class="sub-block-info-position">{{ userInfo.POSITION }}</span>
                        <span class="sub-block-info-department">{{ userInfo.DEPARTMENT }}</span>
                    </div>
                </div>
            </div>
            <div class="sub-block-info">
                <disable-input v-for="(process, index) in processInfo"
                       :title="process.title"
                       :value="process.value"
                       :key="'process_info_' + index"
                />
            </div>
          <div class="sub-block-progress">
            <div class="sub-block-progress-row">
              <span>{{ localize.TIME_PROCESS }}</span>
              <div class="line-default">
                <div class="line-progress" :style='"width: " + (progressTime < 0 ? 0 : (progressTime > 100 ? 100 : progressTime)) + "%"'></div>
                <span class="line-progress-value">{{ progressTime < 0 ? 0 : (progressTime > 100 ? 100 : progressTime) }}%</span>
              </div>
            </div>
            <div class="sub-block-progress-row">
              <span>{{ localize.PROGRESS_PROCESS }}</span>
              <div class="line-default">
                <div class="line-progress" :style='"width: " + (progressValue > 100 ? 100 : progressValue) + "%"'></div>
                <span class="line-progress-value">{{ progressValue > 100 ? 100 : progressValue }}%</span>
              </div>
            </div>
          </div>
        </div>
    </div>
</template>

<script>
    import {mapState} from 'vuex';
    import DisableInput from "./controls/DisableInput";
    export default {
        name: "HeadInfo",
        components: {
            DisableInput
        },
        computed: {
            ...mapState([
                'processInfo',
                'userInfo',
                'progressTime',
                'progressValue'
            ]),
            localize()
            {
                return Object.freeze({
                    'ABOUT_PROCESS': BX.message("ABOUT_PROCESS"),
                    'ASSESSED': BX.message("ASSESSED"),
                    'TIME_PROCESS': BX.message("TIME_PROCESS"),
                    'PROGRESS_PROCESS': BX.message("PROGRESS_PROCESS")
                })
            }
        }
    }
</script>