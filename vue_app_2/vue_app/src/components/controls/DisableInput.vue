<template>
    <div class="sub-block-info-element">
        <span class="sub-block-info-title">{{title }}</span>
        <input class="sub-block-info-value" disabled="disabled" :value="value"/>
    </div>

</template>

<script>
    export default {
        name: "DisableInput",
        props: {
            title: String,
            value: String,
        }
    }
</script>
