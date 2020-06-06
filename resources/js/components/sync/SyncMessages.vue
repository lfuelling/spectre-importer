<template>
    <div v-if="!this.isEmpty(this.errors) || !this.isEmpty(this.warnings) || !this.isEmpty(this.messages)">
        <div v-if="!this.isEmpty(this.errors)">
            <strong class="text-danger">Error(s)</strong>
            <ul>
                <li v-for="(errorList, line) in this.errors">Line #{{ line }}:
                    <span v-if="1 === errorList.length"> {{ errorList[0] }}</span>
                    <ul v-if="errorList.length > 1">
                        <li v-for="(error) in errorList"> ({{ errorList.length }}) {{ error }}</li>
                    </ul>
                </li>
            </ul>
        </div>
        <div v-if="!this.isEmpty(this.warnings)">
            <strong class="text-warning">Warning(s)</strong>
            <ul>
                <li v-for="(warningList, line) in this.warnings">Line #{{ line }}:
                    <span v-if="1 === warningList.length"> {{ warningList[0] }}</span>
                    <ul v-if="warningList.length > 1">
                        <li v-for="(warning) in warningList">({{ warningList.length }}) {{ warning }}</li>
                    </ul>
                </li>
            </ul>
        </div>
        <div v-if="!this.isEmpty(this.messages)">
            <strong class="text-info">Message(s)</strong>
            <ul>
                <li v-for="(messageList, line) in this.messages">Line #{{ line }}:
                    <span v-if="1 === messageList.length" v-html="messageList[0]"/>
                    <ul v-if="messageList.length > 1">
                        <li v-for="(message) in messageList" v-html="message"/>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
    export default {
        name: "ImportMessages",
        props: {
            messages: {
                type: [Array, Object],
                default: function () {
                    return {};
                }
            },
            warnings: {
                type: [Array, Object],
                default: function () {
                    return {};
                }
            },
            errors: {
                type: [Array, Object],
                default: function () {
                    return {};
                }
            },
        },
        methods: {
            isEmpty(obj) {
                for (let x in obj) {
                    return false;
                }
                return true;
            }
        }
    }
</script>

<style scoped>

</style>
